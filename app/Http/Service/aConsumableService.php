<?php

namespace App\Http\Service;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aConsumableRepositoryImpl;
use App\Http\Repository\aFakturRepositoryImpl;
use App\Http\Repository\aJurnalRepositoryImpl; 
use App\Http\Repository\aSupplierRepositoryImpl;

use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl; 
use App\Http\Repository\aPurchaseOrderRepositoryImpl;
use App\Traits\FifoTrait;
use App\Http\Repository\bBillingRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aJaminanRepositoryImpl;

class aConsumableService extends Controller
{
    use AutoNumberTrait;
    use FifoTrait;
    private $aDeliveryOrder;
    private $aBarangRepository;
    private $aSupplierRepository;
    private $aPurchaseOrderRepository;
    private $aBukuStok;
    private $aStok;
    private $aHna; 
    private $aJurnal;
    private $aConsumableRepository;
    private $aMasterUnitRepository;
    private $ahnaRepository;
    private $billingRepository;
    private $visitRepository;
    private $jaminanRepository;

    public function __construct(
        aDeliveryOrderRepositoryImpl $aDeliveryOrder,
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $aSupplierRepository,
        aPurchaseOrderRepositoryImpl $aPurchaseOrderRepository,
        aStokRepositoryImpl $aStok,
        aHnaRepositoryImpl $aHna,
        aJurnalRepositoryImpl $aJurnal,
        aConsumableRepositoryImpl $aConsumableRepository,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aHnaRepositoryImpl $ahnaRepository,
        bBillingRepositoryImpl $billingRepository,
        bVisitRepositoryImpl $visitRepository,
        aJaminanRepositoryImpl $jaminanRepository
    ) {
        $this->aDeliveryOrder = $aDeliveryOrder;
        $this->aBarangRepository = $aBarangRepository;
        $this->aSupplierRepository = $aSupplierRepository;
        $this->aPurchaseOrderRepository = $aPurchaseOrderRepository;
        $this->aStok = $aStok;
        $this->aHna = $aHna;
        $this->aJurnal = $aJurnal;
        $this->aConsumableRepository = $aConsumableRepository;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->ahnaRepository = $ahnaRepository;
        $this->billingRepository = $billingRepository;
        $this->visitRepository = $visitRepository;
        $this->jaminanRepository = $jaminanRepository;
    }

    public function addConsumableHeader(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitCode" => "required", 
            "Notes" => "required" 
        ]);
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('kode Unit Order tidak ditemukan !', []);
        }
        
        try {
            // Db Transaction
            DB::beginTransaction(); 

            $getmax = $this->aConsumableRepository->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->ConsumableNumber($request, $TransactionCode);

            $this->aConsumableRepository->addConsumableHeader($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Transaksi Pemakaian barang berhasil dibuat !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
         
    } 
    public function addConsumableDetailv2(Request $request){
        // validate 
        $request->validate([
            "TransactionCode" => "required", 
            "ProductCode" => "required",  
            "ProductSatuan" => "required",
            "KonversiQty" => "required",
            "UnitTujuan" => "required", 
            "Satuan_Konversi" => "required", 
            "Konversi_QtyTotal" => "required", 
            "ProductName" => "required",
            "Qty" => "required",
            "Hpp" => "required",
            "Persediaan" => "required",
            "UserCreate" => "required", 
            "Total" => "required"
        ]); 
        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }

        // // cek kode barangnya ada ga
        if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
            return $this->sendError('Kode Barang tidak ditemukan !', []);
        }

        // // cek sudah pernah di entri
        if ($this->aConsumableRepository->getConsumableDetailbyIDBarangV2($request,$request->ProductCode)->count() > 0) {
            return $this->sendError('Kode Barang Sudah pernah diinput !', []);
        }

        // Validasi Stok ada gak
            $cekstok = $this->aStok->cekStokbyIDBarangV2($request->ProductCode, $request->UnitTujuan)->count();
            if ( $cekstok < 1) {
                return  $this->sendError('Qty Stok Tidak ada diLayanan Tujuan Ini ! ' , []);
            }

        // validasi stok cukup engga 
            # code...
            //  KHUSUS PAKAI BARANG CUKUP SATUAN TERKECIL LANGSUNG AJA.
            $cekstok = $this->aStok->cekStokbyIDBarangV2($request->ProductCode, $request->UnitTujuan)->first();
            $getdatadetilmutasi = $this->aConsumableRepository->getConsumableDetailbyIDBarangV2($request, $request->ProductCode);
            $vGetMutasiDetil =  $getdatadetilmutasi->first();
            
            if($getdatadetilmutasi->count() < 1 ){
                $stokCurrent = (float)$cekstok->Qty;
                if ($stokCurrent < $request->Qty) {
                    return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $request->Qty . ' ! ', []);
                }
            }else{
                $stokCurrent = (float)$cekstok->Qty;
                $getStokPlus = $vGetMutasiDetil->Qty + $stokCurrent;
                $stokminus = $getStokPlus - $request->Konversi_QtyTotal;
                if ($stokminus < 0) {
                    return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $request->Qty . ' ! ', []);
                } 
            }
            try {
                // Db Transaction
                DB::beginTransaction();
                
                $this->aConsumableRepository->addConsumableDetailV2($request); 
                $this->aConsumableRepository->editOutstandingConsumable($request,$request->TransactionCode);

                $getHppBarang = $this->ahnaRepository->getHppAverage($request)->first()->first();
                $xhpp = $getHppBarang->NominalHpp;
                $this->fifoConsumable($request,$request,$xhpp);

                DB::commit();
                return $this->sendResponse([], 'Data Pemakaian barang berhasil ditambahkan !');
            }catch (Exception $e) {
                DB::rollBack();
                Log::info($e->getMessage());
                return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
            }
    }
    public function addConsumableDetail(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required", 
            "UnitTujuan" => "required",  
            "Notes" => "required",
            "TotalQtyOrder" => "required",
            "TotalRow" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);

        

        // validasi 
        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }

        // validasi Kode
        foreach ($request->Items as $key) {
            # code...
            // // cek kode barangnya ada ga
            if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                return $this->sendError('Kode Barang tidak ditemukan !', []);
            }
        }
        // Validasi Stok ada gak
        foreach ($request->Items as $key) {
            # code...
            // cek kode barangnya ada ga
            $cekstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitTujuan)->count();
            
            if ( $cekstok < 1) {
                return  $this->sendError('Qty Stok '.$key['ProductName'].' Tidak ada diLayanan Tujuan Ini ! ' , []);
            }
        }

        //cek jika billing sudah ada payment atau belum
        $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
        if ($cekbill > 0){        
            if ($this->billingRepository->getBilling1byTrsID($request->TransactionCode)->first()->ID_TRS_Payment != null){
                return $this->sendError('Billing Sudah Ada Payment ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }

         $noregpass = $request->NoRegistrasi;
        // if ($noreg != 0 || $noreg != null){
        //     $noregpass = $noreg;
        // }else{
        //     $noregpass = $request->TransactionCode;
        // }
        //cek jika billing sudah diclose atau belum
        if ($this->billingRepository->getBillingClose($noregpass)->count() > 0){
            return $this->sendError('Billing Sudah Diclose ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
        } 

        // if ($this->returJualRepository->getReturJualDetailbySalesCode($request->TransactionCode)->count() > 0) {
        //     return $this->sendError('Sales Number Sudah Pernah Ada Riwayat Transaksi Retur ! Silahkan Dicek Kembali !', []);
        // }

        // validasi stok cukup engga
        foreach ($request->Items as $key) {
            # code...
             //  KHUSUS PAKAI BARANG CUKUP SATUAN TERKECIL LANGSUNG AJA.
            $cekstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
            $getdatadetilmutasi = $this->aConsumableRepository->getConsumableDetailbyIDBarang($request, $key);
            $vGetMutasiDetil =  $getdatadetilmutasi->first();
             
            if($getdatadetilmutasi->count() < 1 ){
                $stokCurrent = (float)$cekstok->Qty;
                if ($stokCurrent < $key['Qty']) {
                    return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['Qty'] . ' ! ', []);
                }
            }else{
                $stokCurrent = (float)$cekstok->Qty;
                $getStokPlus = $vGetMutasiDetil->Qty + $stokCurrent;
                $stokminus = $getStokPlus - $key['Konversi_QtyTotal'];
                if ($stokminus < 0) {
                    return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['Qty'] . ' ! ', []);
                } 
            }
        }
        try {
            // Db Transaction
            DB::beginTransaction();

            $this->aJurnal->delJurnalHdr($request);
            $this->aJurnal->delJurnalDtl($request);

            //insert billingnya jika memakai nomor registrasi
            if ($request->NoRegistrasi != ''){
                $tipereg = substr($request->NoRegistrasi, 0, 2);
                if ($tipereg == 'RJ'){
                    $getdataregpasien = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
                }elseif($tipereg == 'RI'){
                    $getdataregpasien = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
                    $request['KodeKelas'] = $getdataregpasien->KelasID_Akhir;
                }else{
                    return  $this->sendError('Nomor Registrasi Tidak Valid ! ' , []);
                }
                $request['TotalSales'] = 0;
                $request['SubtotalQtyPrice'] = 0;
                $request['Discount_Prosen'] = 0;
                $request['Discount'] = 0;
                $request['Subtotal'] = 0;
                $request['Grandtotal'] = 0;
                $request['NoMr'] = $getdataregpasien->NoMR;
                $request['NoEpisode'] = $getdataregpasien->NoEpisode;
                $request['GroupJaminan'] = $getdataregpasien->TipePasien;
                $request['KodeJaminan'] = $getdataregpasien->KodeJaminan;
                $request['IdUnit'] = $getdataregpasien->IdUnit;
                // // billinga
                $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
                //cek jika sudah ada di table
                if ( $cekbill > 0) {
                    //update
                    $this->billingRepository->updateHeader($request,$request->TransactionCode);
                }else{
                    //insert
                    $this->billingRepository->insertHeader($request,$request->TransactionCode);
                }
            }


            foreach ($request->Items as $key) {
                $getdatadetilmutasi = $this->aConsumableRepository->getConsumableDetailbyIDBarang($request,$key);
                    // get Hpp Average 
                    $getHppBarang = $this->ahnaRepository->getHppAverage($key)->first()->first();
                    $xhpp = $getHppBarang->NominalHpp;
                    // get Hpp Average
                if($getdatadetilmutasi->count() < 1){
                    if ($key['Qty'] > 0) {
                        $this->aConsumableRepository->addConsumableDetail($request, $key); 
                        $this->fifoConsumable($request,$key,$xhpp); 

                        
                if ($request->NoRegistrasi != ''){
                                                $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                        $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                        $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                        'Farmasi',$request->KodeKelas,$key['Qty'],0,0,
                        0,0,0,0,'','','','FARMASI');
                }
                    }
                }else{
                    // jika sudah ada
                    $showData = $getdatadetilmutasi->first();
                   
                    $mtKonversi_QtyTotal = $showData->Qty;
                    $mtQtyMutasi = $showData->Qty;

                   if($mtKonversi_QtyTotal <> $key['Konversi_QtyTotal']){ // Dirubah jika Qty nya ada Perubahan Aja
                        // $goQtyMutasiSisaheaderBefore = $mtQtyMutasi + $key['Qty'];
                        // $goQtyMutasiSisaheaderAfter = $goQtyMutasiSisaheaderBefore - $key['Qty'];
                        // $goQtyMutasiSisaKovenrsiBefore = $mtKonversi_QtyTotal + $key['Konversi_QtyTotal'];
                        // $goQtyMutasiSisaKovenrsiAfter = $goQtyMutasiSisaKovenrsiBefore - $key['Konversi_QtyTotal'];
                        $this->aConsumableRepository->editConsumableDetailbyIdBarang($request,$key);
                        // replace stok ke awal
                        //$getCurrentStok = $this->aStok->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                        //$totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                      //  $this->aStok->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                        $this->aStok->deleteBukuStok($request,$key,"CM",$request->UnitTujuan);  
                        $this->aStok->deleteDataStoks($request,$key,"CM",$request->UnitTujuan);  
                        $this->fifoConsumable($request,$key,$xhpp);

                        if ($request->NoRegistrasi != ''){
                        //update billing detail
                        $this->billingRepository->updateDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                        $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                        $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                        'Farmasi',$request->KodeKelas,$key['Qty'],0,0,
                        0,0,0,0,'','','','FARMASI');
                        }
                   }  
                } 

                // // insert billing detail
                // if ($request->NoRegistrasi != ''){
                //     // $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                //     // $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                //     // $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                //     // 'Farmasi',$request->KodeKelas,$key['Qty'],0,0,
                //     // 0,0,0,0,'','','','FARMASI');

                //     if ($cekbill > 0 ){
                //         //update billing detail
                //         $this->billingRepository->updateDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                //         $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                //         $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                //         'Farmasi',$request->KodeKelas,$key['Qty'],0,0,
                //         0,0,0,0,'','','','FARMASI');
                //     }else{
                //         // insert billing detail
                //        $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                //        $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                //        $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                //        'Farmasi',$request->KodeKelas,$key['Qty'],0,0,
                //        0,0,0,0,'','','','FARMASI');
                //     }
                // }

                // INSERT JURNAL 
                    $note = 'Persediaan Pemakaian Barang '. $key['ProductName'].' No. Pemakaian : ' . $request->TransactionCode . ' Qty : ' . $key['Qty'];
                    $noteHpp = 'Hpp Persediaan Pemakaian Barang '. $key['ProductName'].' No. Pemakaian : ' . $request->TransactionCode . ' Qty : ' . $key['Qty'];
                    $cekrek = $this->aJurnal->getRekeningPersediaaan($key['ProductCode']);  
                  
                    $this->aJurnal->addJurnalDetailKreditPersediaanGlobal(
                        $note, $cekrek->first()->RekPersediaan,
                        $request->TransactionCode,
                        $key['Qty'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitTujuan
                    );  

                    $this->aJurnal->addJurnalDetailDebetHppGlobal(
                        $noteHpp, $cekrek->first()->RekHpp,
                        $request->TransactionCode,
                        $key['Qty'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitTujuan
                    ); 

                // INSERT JURNAL
            }

            //jika sudah ada trs dan ada yang didelete
            //$salesdtl = $this->aSalesRepository->getSalesDetailbyID($request->TransactionCode);
            $dtlconsumable = $this->aConsumableRepository->getConsumableDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $key_dtl) {
                $isdeleted = true;
                foreach ($request->Items as $key) {
                    if ($key['ProductCode'] == $key_dtl->ProductCode){
                        $isdeleted = false;
                    }
                }

                if ($isdeleted){
                    $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($key_dtl->ProductCode,$request,'CM');
                    foreach ($cekBuku as $data) {
                       $asd = $data;
                    } 
                        $request['Void'] = '1';
                        $request['UserVoid'] = $request->UserCreate;
                        $request['ReasonVoid'] = '';
                        $request['ProductCode'] = $key_dtl->ProductCode;
                        $this->aStok->addBukuStokInVoidFromSelect($asd,'CM_V',$request);
                        $this->aStok->addDataStoksInVoidFromSelect($asd,'CM_V',$request);

                        $this->aConsumableRepository->voidConsumablebyItem($request);
                        $this->billingRepository->voidBillingPasienOneByProductCode($request);
                        //$this->billingRepository->voidBillingPasienTwoByProductCode($request);
                    }
            }

            //insert billing pdp
            if ($request->NoRegistrasi != ''){
                $request['Void'] = '1';
                $request['UserVoid'] = $request->UserCreate;
                $this->billingRepository->voidBillingPasienTwo($request);
                $dataBilling1 = $this->billingRepository->getBillingFo1($request);
                foreach ($dataBilling1 as $dataBilling1) {
                    $this->billingRepository->insertDetailPdp($dataBilling1);
                } 
            }

            // update tabel header
            $notex = 'Pemakaian Barang , No. Pemakaian : ' . $request->TransactionCode;
                    
            $this->aJurnal->addJurnalHeaderConsumable($request, $notex);
            $this->aConsumableRepository->editConsumable($request);
            DB::commit();
            return $this->sendResponse([], 'Data Pemakaian barang berhasil ditambahkan !');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    // public function finish(Request $request)
    // {
    //     // validate 
    //     $request->validate([
    //         "TransactionCode" => "required", 
    //         "UnitCode" => "required",  
    //         "Notes" => "required",
    //         "TotalQtyOrder" => "required",
    //         "TotalRow" => "required",
    //         "TransactionDate" => "required",
    //         "UserCreate" => "required"
    //     ]);


    //     // validasi 
    //     // // cek ada gak datanya
    //     if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
    //         return $this->sendError('Consumable Number Not Found !', []);
    //     }

    //      // validasi Kode
    //      foreach ($request->Items as $key) {
    //         # code...
    //         // // cek kode barangnya ada ga
    //         if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
    //             return $this->sendError('Product Not Found !', []);
    //         }
    //     }
    //     // Validasi Stok ada gak
    //     foreach ($request->Items as $key) {
    //         # code...
    //         // cek kode barangnya ada ga
    //         $cekstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitCode)->count();
            
    //         if ( $cekstok < 1) {
    //             return  $this->sendError('Qty Stok Tidak ada diLayanan Tujuan Ini ! ' , []);
    //         }
    //     }

    //     // validasi stok cukup engga
    //     foreach ($request->Items as $key) {
    //         # code...
    //          //  KHUSUS PAKAI BARANG CUKUP SATUAN TERKECIL LANGSUNG AJA.
    //         $cekstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitCode)->first();
    //         $stokCurrent = (float)$cekstok->Qty;
    //         if ($stokCurrent < $key['Qty']) {
    //             return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['Qty'] . ' ! ', []);
    //         }
    //     }



    //     try {
    //         // Db Transaction
    //         DB::beginTransaction(); 

    //         // foreach ($request->Items as $key) {
    //         //     $getdatadetilmutasi = $this->aConsumableRepository->getMutasiDetailbyIDBarang($request,$key);
    //         //     if($getdatadetilmutasi->count() < 1){
    //         //         if ($key['Konversi_QtyTotal'] > 0) {
    //         //             $this->aMutasiRepository->addMutasiDetail($request, $key); 
    //         //         }
    //         //     }else{
                
    //         //     }
    //         // }
    //         DB::commit();
    //         return $this->sendResponse([], 'Items Add Successfully !');
    //     }catch (Exception $e) {
    //         DB::rollBack();
    //         Log::info($e->getMessage());
    //         return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
    //     }
    // }
    public function voidConsumable(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required", 
            "UnitCode" => "required",  
            "ReasonVoid" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);
          // validasi 
        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Kode Unit Order tidak ditemukan !', []);
        }

        //cek jika billing sudah ada payment atau belum
        $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
        if ($cekbill > 0){        
            if ($this->billingRepository->getBilling1byTrsID($request->TransactionCode)->first()->ID_TRS_Payment != null){
                return $this->sendError('Billing Sudah Ada Payment ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }

        $noregpass = $this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->first()->NoRegistrasi;
        // if ($noreg != 0 || $noreg != null){
        //     $noregpass = $noreg;
        // }else{
        //     $noregpass = $request->TransactionCode;
        // }
        //cek jika billing sudah diclose atau belum
        if ($this->billingRepository->getBillingClose($noregpass)->count() > 0){
            return $this->sendError('Billing Sudah Diclose ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
        } 


        try {
            // Db Transaction
            DB::beginTransaction(); 
            $reff_void = 'CM_V';
            // Load Data All Do Detil Untuk Di Looping 
            $dtlconsumable = $this->aConsumableRepository->getConsumableDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $key2) {
                //$QtyPakai = $key2->Qty;
                // $Konversi_QtyTotal = $key2->Konversi_QtyTotal;

                // $cekqtystok = $this->aStok->cekStokbyIDBarangOnly($key2->ProductCode,$request);
                //     foreach ($cekqtystok as $valueStok) {
                //         $datastok = $valueStok->Qty;
                //     } 
               // $sisaStok = $datastok + $Konversi_QtyTotal;
                //$this->aStok->updateStokPerItemBarang($request, $key2->ProductCode, $sisaStok);

                    // buku 
                    $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($key2->ProductCode,$request,'CM');
                    foreach ($cekBuku as $data) {
                       $asd = $data;
                    } 
                    $this->aStok->addBukuStokInVoidFromSelect($asd,'CM_V',$request);
                    $this->aStok->addDataStoksInVoidFromSelect($asd,'CM_V',$request);
            }
            
            // void billing transaction
            $this->billingRepository->voidBillingPasien($request);
            $this->billingRepository->voidBillingPasienOne($request);
            $this->billingRepository->voidBillingPasienTwo($request);
        
            $this->aConsumableRepository->voidConsumableDetailAllOrder($request);
            $this->aConsumableRepository->voidConsumable($request);

            DB::commit();
            return $this->sendResponse([], 'Transaksi Pemakaian Barang berhasil dihapus !');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal !', $e->getMessage());
        }
    }
    public function voidConsumableDetailbyItem(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required", 
            "ProductCode" => "required",
            "UnitCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Kode Unit Order tidak ditemukan !', []);
        }
        // cek kode unit ini bener ga atas transaksi ini
        $cekdodetil = $this->aConsumableRepository->getConsumablebyIDTransactionandUnitID($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
        }
        // // cek kode barangnya ada ga
        if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
            return $this->sendError('Product Not Found !', []);
        } 
        // cek aktif engga
        $cekdodetil = $this->aConsumableRepository->getConsumableDetailbyIDandProductCode($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Barang Sudah di Batalkan !', []);
        }
        try {
            // Db Transaction
            DB::beginTransaction(); 

            //$dtlDo = $this->aConsumableRepository->getConsumableDetailbyIDandProductCode($request)->first();
            //$Konversi_QtyTotal = $dtlDo->Qty;
            //$cekqtystok = $this->aStok->cekStokbyIDBarangOnly($request->ProductCode,$request);
            // foreach ($cekqtystok as $valueStok) {
            //     $datastok = $valueStok->Qty;
            // } 
            //$sisaStok = $datastok + $Konversi_QtyTotal;
            //$this->aStok->updateStokPerItemBarang($request, $request->ProductCode, $sisaStok);

            // buku 
            $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($request->ProductCode,$request,'CM');
            foreach ($cekBuku as $data) {
               $asd = $data;
            } 
            $this->aStok->addBukuStokInVoidFromSelect($asd,'CM_V',$request);
            $this->aStok->addDataStoksInVoidFromSelect($asd,'CM_V',$request);
            
            //void billing
            $this->billingRepository->voidBillingPasienOneByProductCode($request);
            $this->billingRepository->voidBillingPasienTwoByProductCode($request);

            $this->aConsumableRepository->voidConsumablebyItem($request);

            DB::commit();
            return $this->sendResponse([], 'Transaksi Pakai Barang detail berhasil di Hapus !');

        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal !', $e->getMessage());
        }
    }
    public function getConsumablebyID($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);

        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }
        try {
            // Db Transaction
            $data = $this->aConsumableRepository->getConsumablebyID($request->TransactionCode);
            return $this->sendResponse($data, 'No. Transaksi Pemakaian Barang ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal !', $e->getMessage());
        }

    }
    public function getConsumableDetailbyID($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);
        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }
        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumableDetailbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang detail tidak ditemukan !', []);
        }
        try {
            // Db Transaction
            $data = $this->aConsumableRepository->getConsumableDetailbyID($request->TransactionCode);
            return $this->sendResponse($data, 'No. Transaksi Pemakaian Barang ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Gagal diproses !', $e->getMessage());
        }

    }
    public function getConsumablebyDateUser($request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);
        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyDateUser($request)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }
        try {
            // Db Transaction
            $data = $this->aConsumableRepository->getConsumablebyDateUser($request);
            return $this->sendResponse($data, 'No. Transaksi Pemakaian Barang ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Gagal diproses !', $e->getMessage());
        }
    }
    public function getConsumablebyPeriode($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required"
        ]);
        // // cek ada gak datanya
        if ($this->aConsumableRepository->getConsumablebyPeriode($request)->count() < 1) {
            return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
        }
        try {
            // Db Transaction
            $data = $this->aConsumableRepository->getConsumablebyPeriode($request);
            return $this->sendResponse($data, 'No. Transaksi Pemakaian Barang ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Gagal diproses !', $e->getMessage());
        }
    }

    public function addConsumableDetailPaket(Request $request)
    {
        try {
            // Db Transaction
            DB::beginTransaction(); 
            $request->validate([
                // "TransactionCode" => "required",
                // // "ProductCode" => "required",
                // // "ProductName" => "required",
                // // "Satuan" => "required",
                // // "Satuan_Konversi" => "required",
                // // "KonversiQty" => "required",
                // // "Konversi_QtyTotal" => "required",
                // // "QtyStok" => "required",
                // // "QtyOrderMutasi" => "required",
                // // "QtySisaMutasi" => "required",
                //  "UserAdd" => "required" ,
                // "UnitOrder" => "required",
                // "IdPaket" => "required" 
                "TransactionCode" => "required",
                "UnitTujuan" => "required",
                "IdPaket" => "required" ,
                //"NoRegistrasi" => "required" 
            ]);

                        // validasi 
                // // cek ada gak datanya
                if ($this->aConsumableRepository->getConsumablebyID($request->TransactionCode)->count() < 1) {
                    return $this->sendError('No. Transaksi Pemakaian Barang tidak ditemukan !', []);
                }

                
                // // cek sudah di approved belum 
                // if ($this->aOrderMutasiRepository->getOrderMutasiApprovedbyID($request->TransactionCode)->count() > 0) {
                //     return $this->sendError('Transaksi Order Mutasi sudah di Approve !', []);
                // }
                $message = [];
                //get data paket detail by id header
                $datapaket = $this->aBarangRepository->getDataPaketDetailbyIDHdr($request->IdPaket);
                foreach ($datapaket as $key) {
                    //cek kode barangnya ada ga
                    if ($this->aBarangRepository->getBarangbyId($key->product_id)->count() < 1) {
                        //return $this->sendError('Kode Barang tidak ditemukan !', []);
                        //array_push($message,'Kode barang '.$key->product_id.' tidak ditemukan !');
                        continue;
                    }
                    // // //cek barang dobel gak 
                    $datapass['ProductCode'] = $key->product_id;
                    $getdatadetilmutasi = $this->aConsumableRepository->getConsumableDetailbyIDBarang($request, $datapass);
                    if ($getdatadetilmutasi->count() > 0) {
                        //return $this->sendError('Kode Barang sudah ada sebelumnya, tidak boleh lebih dari 1 !', []);
                        //array_push($message,'Kode barang '.$key->product_id.' sudah ada sebelumnya, tidak boleh lebih dari 1 !');
                        continue;
                    }

                    if ($request->NoRegistrasi != ''){
                        $tipereg = substr($request->NoRegistrasi, 0, 2);
                        if ($tipereg == 'RJ'){
                            $getdataregpasien = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
                        }elseif($tipereg == 'RI'){
                            $getdataregpasien = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
                        }else{
                            return  $this->sendError('Nomor Registrasi Tidak Valid ! ' , []);
                        }
                        $request['GroupJaminan'] = $getdataregpasien->TipePasien;
                        $request['KodeJaminan'] = $getdataregpasien->KodeJaminan;
                        $datajaminan = $this->jaminanRepository->getJaminanAllAktifbyId($request['GroupJaminan'],$request['KodeJaminan'])->first();
                        $datagenform = (object)null; 
                        $datagenform->IDBarang = $key->product_id;
                        $datagenform->IDFormularium = $datajaminan->IDFormularium;
                        $dataformularium = $this->aBarangRepository->getBarangbyIdAndIDFormularium($datagenform);
                        //13112024
                        if ($dataformularium->count() < 1){
                            return $this->sendError('Kode Barang '.$key->product_id.' ID Formularium tidak ditemukan !', []);
                            //continue;
                        }
                    }


                    $databarang = $this->aBarangRepository->getBarangbyId($key->product_id);
                    $datagen['ProductCode'] = $key->product_id;
                    $datagen['ProductName'] = $databarang->first()->{'Product Name'};
                    $datagen['Qty'] = $key->quantity;
                    $datagen['Konversi_QtyTotal'] = $key->quantity;
                    $datagen['Satuan_Konversi'] = $databarang->first()->{'Unit Satuan'};
                    $datagen['KonversiQty'] = $key->quantity;
                    $datagen['Satuan'] = $databarang->first()->Satuan_Beli;
                    array_push($message,$datagen);

                //     $databarang = $this->aBarangRepository->getBarangbyId($key->product_id);
                //     $datapass['ProductCode'] = $key->product_id;
                //     $datastok = $this->aStokRepository->cekStokbyIDBarang($datapass, $request->UnitTujuan);
                //     if ($datastok->count() < 1){
                //         //return $this->sendError('Kode Product tidak ada di stok layanan !', []);
                //             array_push($message,'Barang '. $databarang->first()->{'Product Name'} . '(' .$key->product_id . ') tidak ada di layanan tujuan ini !');
                //             continue;
                //     }

                //     $getdatadetilmutasi = $this->aConsumableRepository->getConsumableDetailbyIDBarang($request, $datapass);
                //     $vGetMutasiDetil =  $getdatadetilmutasi->first();
                //     if($getdatadetilmutasi->count() < 1 ){
                //         $stokCurrent = (float)$datastok->Qty;
                //         if ($stokCurrent < $key['quantity']) {
                //             // return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['quantity'] . ' ! ', []);
                //             array_push($message,'Qty Stok ' . $databarang->first()->{'Product Name'} . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['quantity'] . ' ! ',);
                //             continue;
                //         }
                //     }else{
                //         $stokCurrent = (float)$datastok->Qty;
                //         $getStokPlus = $vGetMutasiDetil->Qty + $stokCurrent;
                //         $stokminus = $getStokPlus - $key['quantity'];
                //         if ($stokminus < 0) {
                //             // return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['quantity'] . ' ! ', []);
                //             array_push($message,'Qty Stok ' . $databarang->first()->{'Product Name'} . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['quantity'] . ' ! ',);
                //             continue;
                //         } 
                //     }
                //     //-------------------------------------------------------------

                //     $this->aJurnal->delJurnalHdr($request);
                //     $this->aJurnal->delJurnalDtl($request);
        


                //     //-----------------------------------------------------------------
                //     // get Hpp Average 
                //     $getHppBarang = $this->ahnaRepository->getHppAverage($datapass)->first()->first();
                //     $xhpp = $getHppBarang->NominalHpp;
                //     //passing data
                //     $datagen['ProductCode'] = $key->product_id;
                //     $datagen['ProductName'] = $databarang->first()->{'Product Name'};
                //     $datagen['Qty'] = $key->quantity;
                //     $datagen['Konversi_QtyTotal'] = $key->quantity;
                //     $datagen['Satuan_Konversi'] = $databarang->first()->Satuan_Beli;
                //     $datagen['KonversiQty'] = $key->quantity;
                //     $datagen['Satuan'] = $databarang->first()->{'Unit Satuan'};
                //     if($getdatadetilmutasi->count() < 1){
                //         if ($key['quantity'] > 0) {
                //             $this->aConsumableRepository->addConsumableDetail($request, $datagen); 
                //             $this->fifoConsumable($request,$datagen,$xhpp); 
                //         }
                //     }else{
                //         // jika sudah ada
                //         $showData = $getdatadetilmutasi->first();
                    
                //         $mtKonversi_QtyTotal = $showData->Qty;
                //         $mtQtyMutasi = $showData->Qty;

                //     if($mtKonversi_QtyTotal <> $key['quantity']){ // Dirubah jika Qty nya ada Perubahan Aja
                //             // $goQtyMutasiSisaheaderBefore = $mtQtyMutasi + $key['Qty'];
                //             // $goQtyMutasiSisaheaderAfter = $goQtyMutasiSisaheaderBefore - $key['Qty'];
                //             // $goQtyMutasiSisaKovenrsiBefore = $mtKonversi_QtyTotal + $key['Konversi_QtyTotal'];
                //             // $goQtyMutasiSisaKovenrsiAfter = $goQtyMutasiSisaKovenrsiBefore - $key['Konversi_QtyTotal'];
                //             $this->aConsumableRepository->editConsumableDetailbyIdBarang($request,$datagen);
                //             // replace stok ke awal
                //             //$getCurrentStok = $this->aStok->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                //             //$totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                //         //  $this->aStok->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                //             $this->aStok->deleteBukuStok($request,$key,"CM",$request->UnitTujuan);  
                //             $this->aStok->deleteDataStoks($request,$key,"CM",$request->UnitTujuan);  
                //             $this->fifoConsumable($request,$datagen,$xhpp);
                //     }  
                //     } 
                //     //--------------------------------------------------------------------

                //     $datagen = (object)null; 
                //     $datagen->TransactionCode = $request->TransactionCode;
                //     $datagen->ProductCode = $key->product_id;
                //     $datagen->ProductName = $databarang->first()->{'Product Name'};
                //     $datagen->Satuan = $databarang->first()->{'Unit Satuan'};
                //     $datagen->Satuan_Konversi = $databarang->first()->Satuan_Beli;
                //     $datagen->KonversiQty = $key->quantity;
                //     $datagen->Konversi_QtyTotal = $key->quantity;
                //     $datagen->QtyStok = $datastok->first()->Qty;
                //     $datagen->QtyOrderMutasi = $key->quantity;
                //     $datagen->QtySisaMutasi = $qtysisa;
                //     $datagen->UserAdd = $request->UserAdd;

                // // // //cek barang dobel gak 
                // if ($this->aOrderMutasiRepository->getItemsDouble($datagen)->count() > 0) {
                //     //return $this->sendError('Kode Barang sudah ada sebelumnya, tidak boleh lebih dari 1 !', []);
                //     array_push($message,'Kode barang '.$key->product_id.' sudah ada sebelumnya, tidak boleh lebih dari 1 !');
                //     continue;
                // }

                //     $getHppBarang = $this->ahnaRepository->getHppAveragebyCode($key->product_id)->first()->first();
                //     $xhpp = $getHppBarang->NominalHpp;
                //     $this->aOrderMutasiRepository->addOrderMutasiDetail($datagen, $xhpp);
                  
                }
                
            //DB::commit();
            return $this->sendResponse($message, 'Order Mutasi Detail berhasil di tambahkan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaksi tidak dapat di proses !', $e->getMessage());
        }
    }
}