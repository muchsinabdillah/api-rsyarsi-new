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
        bVisitRepositoryImpl $visitRepository
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
                return  $this->sendError('Qty Stok Tidak ada diLayanan Tujuan Ini ! ' , []);
            }
        }

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
                   }  
                } 

                // insert billing detail
                if ($request->NoRegistrasi != ''){
                    $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                    $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                    $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                    'Farmasi',$request->KodeKelas,$key['Qty'],0,0,
                    0,0,0,0,'','','','FARMASI');
                }
            }

            //insert billing pdp
            if ($request->NoRegistrasi != ''){
                $dataBilling1 = $this->billingRepository->getBillingFo1($request);
                foreach ($dataBilling1 as $dataBilling1) {
                    $this->billingRepository->insertDetailPdp($dataBilling1);
                } 
            }

            // update tabel header
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
}