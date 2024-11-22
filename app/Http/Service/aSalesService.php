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
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aTrsResepRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;
use App\Http\Repository\aReturJualRepositoryImpl;
use App\Http\Repository\aSalesRepositoryImpl;
use App\Http\Repository\bBillingRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Traits\FifoTrait;
use App\Http\Repository\bAntrianFarmasiRepositoryImpl;
use App\Http\Repository\UserRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\aPabrikRepositoryImpl;
use App\Http\Repository\bAntrianRepositoryImpl;
use App\Http\Repository\bAppointmentRepositoryImpl;
use App\Http\Repository\bKamarOperasiRepositoryImpl;
use App\Http\Repository\bMedicalRecordRepositoryImpl;
use App\Http\Repository\aScheduleDoctorRepositoryImpl;

class aSalesService extends Controller
{
    use AutoNumberTrait;
    use FifoTrait;
    private $trsResepRepository;
    private $aDeliveryOrderRepository;
    private $aBarangRepository;
    private $asupplierRepository;
    private $sStokRepository;
    private $aHnaRepository; 
    private $aMasterUnitRepository; 
    private $aSalesRepository; 
    private $visitRepository;
    private $billingRepository;
    private $returJualRepository;
    private $aAntrianFarmasiRepository;

    public function __construct(
        aTrsResepRepositoryImpl  $trsResepRepository,
        aDeliveryOrderRepositoryImpl $aDeliveryOrderRepository,
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $asupplierRepository,
        aStokRepositoryImpl $sStokRepository,    
        aHnaRepositoryImpl $aHnaRepository,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aSalesRepositoryImpl $aSalesRepository,
        bVisitRepositoryImpl $visitRepository,
        bBillingRepositoryImpl $billingRepository,
        aReturJualRepositoryImpl $returJualRepository,
        bAntrianFarmasiRepositoryImpl $aAntrianFarmasiRepository
    ) {
        $this->trsResepRepository = $trsResepRepository;
        $this->aDeliveryOrderRepository = $aDeliveryOrderRepository;
        $this->aBarangRepository = $aBarangRepository;
        $this->asupplierRepository = $asupplierRepository;
        $this->sStokRepository = $sStokRepository;
        $this->aHnaRepository = $aHnaRepository;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->aSalesRepository = $aSalesRepository;
        $this->visitRepository = $visitRepository;
        $this->billingRepository = $billingRepository;
        $this->returJualRepository = $returJualRepository;
        $this->aAntrianFarmasiRepository = $aAntrianFarmasiRepository;
    }

    public function addSalesHeader(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitOrder" => "required", 
            "UnitTujuan" => "required", 
            //"NoRegistrasi" => "required", 
            "Group_Transaksi" => "required", 
            "Notes" => "required" ,
            //"JenisPasien" => "required" ,
            "GroupJaminan" => "required" ,
            "Jaminan" => "required" ,
            "KodeJaminan" => "required" ,
        ]);

        
        try {
            // Db Transaction
            DB::beginTransaction(); 

            if ($this->aMasterUnitRepository->getUnitById($request->UnitOrder)->count() < 1) {
                return $this->sendError('Unit Order Code Not Found !', []);
            }
            if ($this->aMasterUnitRepository->getUnitById($request->UnitTujuan)->count() < 1) {
                return $this->sendError('Unit Order Sales Not Found !', []);
            }

            if ($request->JenisPasien == 'Karyawan'){
                if ($request->NIP_Karyawan == null || $request->NIP_Karyawan == ''){
                    return $this->sendError('NIP Karyawan Kosong !', []);
                }
            }else{
                $request['NIP_Karyawan'] = null;
            }


            // //Cek Di table OrderResep
            // if($request->Group_Transaksi == "RESEP"){
            //     if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->count() < 1) {
            //         return $this->sendError('No Resep Not Found !', []);
            //     }
            // }
            
            //cek iter
            if($request->Group_Transaksi == "RESEP"){
                if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->first()->Iter < $this->aSalesRepository->getSalesbyNoResep($request)->count()){
                    return $this->sendError('Resep sudah pernah dibuat dan berada batas iter resep ! Cek di [List Penjualan] !', []);
                }

                //tambahan 05-11-2024 code:05112024 dan antrian
                $getiter = $this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->first()->IterRealisasi;
                if ($getiter == null){
                    $getiter = 0;
                }else{
                    $getiter += 1;
                }
                $this->trsResepRepository->updateIterReal($request->NoResep,$getiter);
            }
            $getmax = $this->aSalesRepository->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->SalesResepNumber($request, $TransactionCode);
            

            $this->aSalesRepository->addSalesHeader($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Sales Create Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addSalesDetailV2(Request $request)
    {
        $request->validate([
           'TransactionCode' => "required",
            'ProductCode' => "required",
            'ProductName' => "required",
            'Qty' => "required",
            'QtyResep' => "required",
            'Satuan' => "required",
            'Harga' => "required",
            'Discount' => "required",  
            'Subtotal' => "required",
            'Tax' => "required",
            'Grandtotal' => "required", 
            'Konversi_QtyTotal' => "required"
        ]);  
            // // cek ada gak datanya
            if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Sales Number Not Found !', []);
            }

         // validasi Kode
        
            # code...
            // // cek kode barangnya ada ga
            if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
                return $this->sendError('Product Not Found !', []);
            }
           
            # code...
            // cek kode barangnya ada ga
            $cekstok = $this->sStokRepository->cekStokbyIDBarangV2($request->ProductCode, $request->UnitTujuan)->count();
            if ($request->Qty <> 0){ 
                if ( $cekstok < 1) {
                    return  $this->sendError('Qty Stok Tidak ada diLayanan Tujuan Ini ! ' , []);
                }
            }

            // validasi stok cukup engga
        
            # code...
             //  KHUSUS PAKAI BARANG CUKUP SATUAN TERKECIL LANGSUNG AJA.
            $cekstok = $this->sStokRepository->cekStokbyIDBarangV2($request->ProductCode, $request->UnitTujuan)->first();
            $getdatadetilmutasi = $this->aSalesRepository->getSalesDetailbyIDBarangv2($request, $request->ProductCode);
            $vGetMutasiDetil =  $getdatadetilmutasi->first();
            if ($request->Qty <> 0){ 
                if($getdatadetilmutasi->count() < 1 ){
                    $stokCurrent = (float)$cekstok->Qty;
                    if ($stokCurrent < $request->Qty) {
                        return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $request->Qty . ' ! ', []);
                    }
                }else{
                    $stokCurrent = (float)$cekstok->Qty;
                    $getStokPlus = $vGetMutasiDetil->Qty + $stokCurrent;
                    $stokminus = $getStokPlus - $request->Qty;
                    if ($stokminus < 0) {
                        return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $request->Qty . ' ! ', []);
                    } 
                }
            }
            try {
                // Db Transaction
                DB::beginTransaction();
                $getHppBarang = $this->aHnaRepository->getHppAverageV2($request->ProductCode)->first();
                $xhpp = $getHppBarang[0]->NominalHpp;
                // get Hpp Average
             
                $this->aSalesRepository->addSalesDetailV2($request,$xhpp); 
                     
            
                DB::commit();
                return $this->sendResponse([], 'Data Pemakaian barang berhasil ditambahkan !');
            }catch (Exception $e) {
                DB::rollBack();
                Log::info($e->getMessage());
                return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
            }
        
    }
    public function addSalesDetail(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",  
            "Notes" => "required",
            "TotalQtyOrder" => "required",
            "TotalRow" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);

         
        // // cek ada gak datanya
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Sales Number Not Found !', []);
        }

        //cek jika sudah diapprove atau belum
        if ($request->Group_Transaksi == 'RESEP' || $request->Group_Transaksi == 'NON RESEP'){
            // if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->IdOrderResep)->first()->StatusResep >= 2 ){
            //     return $this->sendError('Order Sudah Diapprove ! Silahkan Konfirmasi Ke Bagian Kasir !', []);
            // }
            if ($this->billingRepository->getBillingFo($request)->count() > 0){
                     return $this->sendError('Order Sudah Diapprove ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }
        
        //cek jika billing sudah ada payment atau belum
        $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
        if ($cekbill > 0){        
            if ($this->billingRepository->getBilling1byTrsID($request->TransactionCode)->first()->ID_TRS_Payment != null){
                return $this->sendError('Billing Sudah Ada Payment ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }

        if ($request->NoRegistrasi != 0 || $request->NoRegistrasi != null){
            $noregpass = $request->NoRegistrasi;
        }else{
            $noregpass = $request->TransactionCode;
        }
        //cek jika billing sudah diclose atau belum
        if ($this->billingRepository->getBillingClose($noregpass)->count() > 0){
            return $this->sendError('Billing Sudah Diclose ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
        } 

        if ($this->returJualRepository->getReturJualDetailbySalesCode($request->TransactionCode)->count() > 0) {
            return $this->sendError('Sales Number Sudah Pernah Ada Riwayat Transaksi Retur ! Silahkan Dicek Kembali !', []);
        }

         // validasi Kode
         foreach ($request->Items as $key) {
            # code...
            // // cek kode barangnya ada ga
            if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                return $this->sendError('Product Not Found !', []);
            }
        }
        // Validasi Stok ada gak
        foreach ($request->Items as $key) {
            # code...
            // cek kode barangnya ada ga
            $cekstok = $this->sStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->count();
            if ($key['Qty'] <> 0){ 
                if ( $cekstok < 1) {
                    return  $this->sendError('Qty Stok '.$key['ProductName'].' Tidak ada diLayanan Tujuan Ini ! ' , []);
                }
            }
        }

        // validasi stok cukup engga
        foreach ($request->Items as $key) {
            # code...
             //  KHUSUS PAKAI BARANG CUKUP SATUAN TERKECIL LANGSUNG AJA.
            $cekstok = $this->sStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
            $getdatadetilmutasi = $this->aSalesRepository->getSalesDetailbyIDBarang($request, $key);
            $vGetMutasiDetil =  $getdatadetilmutasi->first();
            if ($key['Qty'] <> 0){ 
                if($getdatadetilmutasi->count() < 1 ){
                    $stokCurrent = (float)$cekstok->Qty;
                    if ($stokCurrent < $key['Qty']) {
                        return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['Qty'] . ' ! ', []);
                    }
                }else{
                    $stokCurrent = (float)$cekstok->Qty;
                    $getStokPlus = $vGetMutasiDetil->Qty + $stokCurrent;
                    $stokminus = $getStokPlus - $key['Qty'];
                    if ($stokminus < 0) {
                        return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Pakai ' . $key['Qty'] . ' ! ', []);
                    } 
                }
            }
        }


        try {
            // Db Transaction
            DB::beginTransaction(); 

            if ($request->Group_Transaksi == 'CONSUMABLE'){
                // billinga
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
                $getdatadetilmutasi = $this->aSalesRepository->getSalesDetailbyIDBarang($request,$key);
                    // get Hpp Average 
                    $getHppBarang = $this->aHnaRepository->getHppAverage($key)->first();
                    $xhpp = $getHppBarang[0]->NominalHpp;
                    // get Hpp Average
                if($getdatadetilmutasi->count() < 1){
                    if ($key['Qty'] > 0) {
                        $this->aSalesRepository->addSalesDetail($request, $key,$xhpp); 
                         // fifo
                         $this->fifoSales($request,$key,$xhpp);

                         if ($request->Group_Transaksi == 'CONSUMABLE'){
                            // insert billing detail
                            $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                            $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                            $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                            'Farmasi',$request->KodeKelas,$key['Qty'],$key['Harga'],$key['SubtotalHarga'],
                            $key['DiscountProsen'],$key['Discount'],$key['Subtotal'],$key['Grandtotal'],'','','','CONSUMABLE');
                         }

                        // //insert billing pdp
                        // $this->billingRepository->insertDetailPdpPerItem($request);

                        // // insert billing pdp
                        // $dataBilling1 = $this->billingRepository->getBillingFo1($request);
                        // foreach ($dataBilling1 as $dataBilling1) {
                        //     $this->billingRepository->insertDetailPdp($dataBilling1);
                        // } 
                        
                        $this->trsResepRepository->editQtyRealbyIDResepandProductCode($request->IdOrderResep,$key['ProductCode'],$key['Qty']);
                    }
                }else{
                    // jika sudah ada
                    $showData = $getdatadetilmutasi->first();
                   
                    $mtKonversi_QtyTotal = $showData->Qty;
                    // $mtQtyMutasi = $showData->Qty;

                   if($mtKonversi_QtyTotal <> $key['Qty']){ // Dirubah jika Qty nya ada Perubahan Aja
                        // $goQtyMutasiSisaheaderBefore = $mtQtyMutasi + $key['Qty'];
                        // $goQtyMutasiSisaheaderAfter = $goQtyMutasiSisaheaderBefore - $key['Qty'];

                        // $goQtyMutasiSisaKovenrsiBefore = $mtKonversi_QtyTotal + $key['Qty'];
                        // $goQtyMutasiSisaKovenrsiAfter = $goQtyMutasiSisaKovenrsiBefore - $key['Qty'];

                         $this->aSalesRepository->editSalesDetailbyIdBarang($request,$key,$xhpp);

                        // replace stok ke awal
                        // $getCurrentStok = $this->sStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                        // $totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                        // $this->sStokRepository->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                        $this->sStokRepository->deleteBukuStok($request,$key,"TPR",$request->UnitTujuan);
                        $this->sStokRepository->deleteDataStoks($request,$key,"TPR",$request->UnitTujuan);

                         // fifo
                         $this->fifoSales($request,$key,$xhpp);

                         
                        if ($request->Group_Transaksi == 'CONSUMABLE'){
                            //update billing detail
                            $this->billingRepository->updateDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                            $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                            $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                            'Farmasi',$request->KodeKelas,$key['Qty'],$key['Harga'],$key['SubtotalHarga'],
                            $key['DiscountProsen'],$key['Discount'],$key['Subtotal'],$key['Grandtotal'],'','','','CONSUMABLE');
                        }
                        
                        $this->trsResepRepository->editQtyRealbyIDResepandProductCode($request->IdOrderResep,$key['ProductCode'],$key['Qty']);
                   }  

                   
                         $this->aSalesRepository->editAturanPakaibyIdBarang($request,$key);
                    
                }


                        //UPDATE SIGNA TERJEMAHAN
                        if ($key['Racik'] <> 0 ){
                            if ($key['RacikHeader'] == 1){
                                if ($key['IDResepDetail'] != 'null'){
                                     $this->trsResepRepository->editSignaTerjemahanbyID($key['IDResepDetail'],$key['AturanPakai']);
                                }
                            }
                        }else{
                            if ($key['IDResepDetail'] != 'null'){
                                $this->trsResepRepository->editSignaTerjemahanbyID($key['IDResepDetail'],$key['AturanPakai']);
                            }
                        }
                
                        // if ( $cekbill > 0) {
                        //     //update
                        //      // insert billing detail
                        //      $this->billingRepository->updateDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                        //      $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                        //      $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                        //      'Farmasi',$request->KodeKelas,$key['Qty'],$key['Harga'],$key['SubtotalHarga'],
                        //      $key['DiscountProsen'],$key['Discount'],$key['Subtotal'],$key['Grandtotal'],'','','','FARMASI');
                        // }else{
                                            //   // insert billing detail
                                            // $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                                            // $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                                            // $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                                            // 'Farmasi',$request->KodeKelas,$key['Qty'],$key['Harga'],$key['SubtotalHarga'],
                                            // $key['DiscountProsen'],$key['Discount'],$key['Subtotal'],$key['Grandtotal'],'','','','FARMASI');
                        //}
  
            }

            //jika didelete dari resep (untuk diupdate ke table orderresepdetail)
            if ($request->Group_Transaksi == 'RESEP'){
                $orderdtl = $this->trsResepRepository->viewOrderResepDetailbyOrderIDV2($request->IdOrderResep);
                foreach ($orderdtl as $key_orderdtl) {
                $isdeleted = true;
                foreach ($request->Items as $key) {
                    if ($key['ProductCode'] == $key_orderdtl->KodeBarang){
                        $isdeleted = false;
                    }
                }
                if ($isdeleted){
                        $this->trsResepRepository->editQtyRealbyIDResepandProductCode($request->IdOrderResep,$key_orderdtl->KodeBarang,0);
                    }
                }
            }

            //jika sudah ada trs dan ada yang didelete
            $salesdtl = $this->aSalesRepository->getSalesDetailbyID($request->TransactionCode);
             foreach ($salesdtl as $key_salesdtl) {
                $isdeleted = true;
                foreach ($request->Items as $key) {
                    if ($key['ProductCode'] == $key_salesdtl->ProductCode){
                        $isdeleted = false;
                    }
                }

                if ($isdeleted){
                    $cekBuku = $this->sStokRepository->cekBukuByTransactionandCodeProduct($key_salesdtl->ProductCode,$request,'TPR');
                        foreach ($cekBuku as $data) {
                            $asd = $data;
                        } 
                        $request['Void'] = '1';
                        $request['UserVoid'] = $request->UserCreate;
                        $request['ReasonVoid'] = '';
                        $request['ProductCode'] = $key_salesdtl->ProductCode;
                        $this->sStokRepository->addBukuStokInVoidFromSelect($asd,'TPR_V',$request);
                        $this->sStokRepository->addDataStoksInVoidFromSelect($asd,'TPR_V',$request);
                        $this->aSalesRepository->voidSalesbyItem($request);
                        
                        if ($request->Group_Transaksi == 'CONSUMABLE'){
                            $this->billingRepository->voidBillingPasienOneByProductCode($request);
                                    //$this->billingRepository->voidBillingPasienTwoByProductCode($request);
                        }
                        $this->trsResepRepository->editQtyRealbyIDResepandProductCode($request->IdOrderResep,$key_salesdtl->ProductCode,0);
                    }
             }

             
                // // update tabel header
                // $this->aSalesRepository->editSales($request);
                // if ( $cekbill > 0) {
                //     //update
                //     $dataBilling1 = $this->billingRepository->getBillingFo1($request);
                //     foreach ($dataBilling1 as $dataBilling1) {
                //         $this->billingRepository->updateDetailPdp($dataBilling1);
                //     } 
                // }else{
                    // $request['Void'] = '1';
                    // $request['UserVoid'] = $request->UserCreate;
                    // $this->billingRepository->voidBillingPasienTwo($request);
                    // $dataBilling1 = $this->billingRepository->getBillingFo1($request);
                    // foreach ($dataBilling1 as $dataBilling1) {
                    //     $this->billingRepository->insertDetailPdp($dataBilling1);
                    // } 
                // }

                if ($request->Group_Transaksi == 'CONSUMABLE'){
                    if ( $cekbill > 0) {
                            $request['Void'] = '1';
                            $request['UserVoid'] = $request->UserCreate;
                            $this->billingRepository->voidBillingPasienTwo($request);
                        }
                        $dataBilling1 = $this->billingRepository->getBillingFo1($request);
                        foreach ($dataBilling1 as $dataBilling1) {
                            $this->billingRepository->insertDetailPdp($dataBilling1);
                        }
                 }

                 //UPDATE REVIEW ORDER RESEP DETAILS
                  $this->trsResepRepository->editReviewbyIDResep($request->IdOrderResep);

                //   if ($request->Group_Transaksi == 'RESEP'){
                //         //insert ke antrian
                //         $request['NoResep'] = $request->IdOrderResep;
                //         $request['StatusResep'] = 'PROCESSED';
                //         $userRepository = new bKamarOperasiRepositoryImpl();
                //         $medrecRepository = new bMedicalRecordRepositoryImpl();
                //         $doctorRepository = new aDoctorRepositoryImpl();
                //         $unitRepository = new aMasterUnitRepositoryImpl();
                //         $appointmenRepository = new bAppointmentRepositoryImpl();
                //         $scheduleRepository = new aScheduleDoctorRepositoryImpl();
                //         $antrianRepository = new bAntrianRepositoryImpl();
                //         $userLoginRepository = new UserRepositoryImpl();
                //         $visitRepository = new bVisitRepositoryImpl();
                //         $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
                //         $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                //                         $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                //                         $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
                //         $user =  $userService->UpdateAntrianFarmasi($request);
                //         if (!$user->getData()->status){
                //             return $this->sendError($user->getData()->message, []);
                //         }
                //   }
                    

                DB::commit();
                return $this->sendResponse([], 'Data Detail Penjualan berhasil disimpan !');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function voidSales(Request $request)
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
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Transaksi Penjualan tidak ditemukan !', []);
        }
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
        }
        
        // $noresep = $this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->NoResep;
        // if ($noresep != 0 && $noresep != null){
        //     if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($noresep)->first()->StatusResep >= 2){
        //         return $this->sendError('Order Sudah Diapprove ! Silahkan Konfirmasi Ke Bagian Kasir !', []);
        //     }
        // }
        //cek jika sudah diapprove atau belum
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->Group_Transaksi == 'RESEP' || $this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->Group_Transaksi== 'NON RESEP'){
            if ($this->billingRepository->getBillingFo($request)->count() > 0){
                return $this->sendError('Order Sudah Diapprove ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }
                
        //cek jika billing sudah ada payment atau belum
        $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
        if ($cekbill > 0){        
            if ($this->billingRepository->getBilling1byTrsID($request->TransactionCode)->first()->ID_TRS_Payment != null){
                return $this->sendError('Billing Sudah Ada Payment ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }

        $noreg = $this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->NoRegistrasi;
        if ($noreg != 0 || $noreg != null){
            $noregpass = $noreg;
        }else{
            $noregpass = $request->TransactionCode;
        }
        //cek jika billing sudah diclose atau belum
        if ($this->billingRepository->getBillingClose($noregpass)->count() > 0){
            return $this->sendError('Billing Sudah Diclose ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
        } 

        if ($this->returJualRepository->getReturJualDetailbySalesCode($request->TransactionCode)->count() > 0) {
            return $this->sendError('Sales Number Sudah Pernah Ada Riwayat Transaksi Retur ! Silahkan Dicek Kembali !', []);
        }


        try {
            // Db Transaction
            DB::beginTransaction(); 
            $reff_void = 'TPR_V';
            // Load Data All Do Detil Untuk Di Looping 
            $dtlconsumable = $this->aSalesRepository->getSalesDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $key2) {
                // $QtyPakai = $key2->Qty;
                // $Konversi_QtyTotal = $key2->Konversi_QtyTotal;

                $cekqtystok = $this->sStokRepository->cekStokbyIDBarangOnly($key2->ProductCode,$request);
 
                    if ( $cekqtystok->count() < 1) {
                        return  $this->sendError('Qty Stok '.$key2->ProductName.' Tidak ada diLayanan Tujuan Ini ! ' , []);
                    }
                    // foreach ($cekqtystok as $valueStok) {
                    //     $datastok = $valueStok->Qty;
                    // } 
                // $sisaStok = $datastok + $Konversi_QtyTotal;
                // $this->sStokRepository->updateStokPerItemBarang($request, $key2->ProductCode, $sisaStok);

                // buku 
                    $cekBuku = $this->sStokRepository->cekBukuByTransactionandCodeProduct($key2->ProductCode,$request,'TPR');
                    foreach ($cekBuku as $data) {
                       $asd = $data;
                    }
                    
                    $this->sStokRepository->addBukuStokInVoidFromSelect($asd,$reff_void,$request);
                    $this->sStokRepository->addDataStoksInVoidFromSelect($asd,$reff_void,$request);
            }

            
            //update qty realisasi
            if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->Group_Transaksi == 'RESEP'){
                $noresep = $this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->NoResep;
                $getiter = $this->trsResepRepository->viewOrderResepbyOrderIDV2($noresep)->first()->IterRealisasi;
                //tambahan 05-11-2024 code:05112024 
                if ($getiter == null || $getiter == 0){
                    $getiter = 0;
                }else{
                    $getiter -= 1;
                }
                $this->trsResepRepository->updateIterReal($noresep,$getiter);
              }
        
            $this->aSalesRepository->voidSalesDetailAllOrder($request);
            $this->aSalesRepository->voidSales($request);


            // void billing transaction
            $this->billingRepository->voidBillingPasien($request);
            $this->billingRepository->voidBillingPasienOne($request);
            $this->billingRepository->voidBillingPasienTwo($request);

            DB::commit();
            return $this->sendResponse([], 'Transaksi Penjualan berhasil dihapus !');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal !', $e->getMessage());
        }
    }
    public function voidSalesDetailbyItem(Request $request)
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
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Sales Number Not Found !', []);
        }
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Unit Order Code Not Found !', []);
        }
        // cek kode unit ini bener ga atas transaksi ini
        $cekdodetil = $this->aSalesRepository->getSalesbyIDTransactionandUnitID($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
        }
        // // cek kode barangnya ada ga
        if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
            return $this->sendError('Kode Barang tidak ditemukan !', []);
        } 
        // cek aktif engga
        $cekdodetil = $this->aSalesRepository->getSalesDetailbyIDandProductCode($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Barang Sudah di Batalkan !', []);
        }
        try {
            // Db Transaction
            DB::beginTransaction(); 

            // $dtlDo = $this->aSalesRepository->getSalesDetailbyIDandProductCode($request)->first();
            // $Konversi_QtyTotal = $dtlDo->Qty;

            // $cekqtystok = $this->sStokRepository->cekStokbyIDBarangOnly($request->ProductCode,$request);
             
            // foreach ($cekqtystok as $valueStok) {
            //     $datastok = $valueStok->Qty;
            // } 
            // $sisaStok = $datastok + $Konversi_QtyTotal;
            // $this->sStokRepository->updateStokPerItemBarang($request, $request->ProductCode, $sisaStok);

            // buku 
            $cekBuku = $this->sStokRepository->cekBukuByTransactionandCodeProduct($request->ProductCode,$request,'TPR');
            foreach ($cekBuku as $data) {
               $asd = $data;
            } 
            $this->sStokRepository->addBukuStokInVoidFromSelect($asd,'TPR_V',$request);
            $this->sStokRepository->addDataStoksInVoidFromSelect($asd,'TPR_V',$request);

            $this->aSalesRepository->voidSalesbyItem($request);

            DB::commit();
            return $this->sendResponse([], 'Transaksi Penjualan berhasil ditambahkan !');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal !', $e->getMessage());
        }
    }
    public function finishSalesTransaction(Request $request)
    {
         // validate 
         $request->validate([
            "TransactionCode" => "required",  
            "UnitTujuan" => "required" ,
            "UnitOrder" => "required" ,
            "Notes" => "required" ,
            "TotalQtyOrder" => "required" ,
            "TotalRow" => "required" ,
            "Discount" => "required" ,
            "Subtotal" => "required" ,
            "Tax" => "required" ,
            "Grandtotal" => "required" ,
            "UserCreateLast" => "required" 
        ]);

        // // cek ada gak datanya
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Transaksi Penjualan tidak ditemukan !', []);
        }

        // if ($request->TotalRow < 1) {
        //     return $this->sendError('There is No Items, Edited Cancelled !', []);
        // }
        // if ($request->TotalQtyOrder < 1) {
        //     return $this->sendError('There is No Qty Items, Edited Cancelled !', []);
        // }
 
        try {
            // Db Transaction
            DB::beginTransaction(); 
            $this->aSalesRepository->editSales($request);
            $this->trsResepRepository->editHasilReviewbyNoTrs($request);
            //update status resep
            $this->trsResepRepository->updateStatusResep($request);
            DB::commit();
            return $this->sendResponse([], 'Transaksi Penjualan Selesai disimpan !');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal !', $e->getMessage());
        }
    }
    public function getSalesbyID($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);
        // // cek ada gak datanya
        $data = $this->aSalesRepository->getSalesbyID($request->TransactionCode);
        if ($data->count() < 1) {
            return $this->sendError('Sales Transaction Number Not Found !', []);
        }
        try {
            // Db Transaction 
            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }
    public function getSalesDetailbyID($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);
        // // cek ada gak datanya
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Sales Transaction Number Not Found !', []);
        }
        // // cek ada gak datanya
        if ($this->aSalesRepository->getSalesDetailbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Sales Transaction Number Detil Not Found !', []);
        }
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesDetailbyID($request->TransactionCode);
            if ($data->count() < 1) {
                return $this->sendError('Sales Transaction Number Detil Not Found !', []);
            }
            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }
    public function getSalesbyDateUser($request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);
        
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesbyDateUser($request);
            // // cek ada gak datanya
            if ($this->aSalesRepository->getSalesbyDateUser($request)->count() < 1) {
                return $this->sendError('Sales Transaction Number Not Found !', []);
            }
            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }
    public function getSalesbyPeriode($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required"
        ]);
        
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesbyPeriode($request);

            // // cek ada gak datanya
            if ($this->aSalesRepository->getSalesbyPeriode($request)->count() < 1) {
                return $this->sendError('Sales Transaction Number Not Found !', []);
            }

            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }

    // public function addSalesDetailTanpaResep(Request $request){
    //     // validate 
    //     $request->validate([
    //         "TransasctionCode" => "required",
    //         "ProductCode" => "required",
    //         "ProductName" => "required",
    //         "QtyStok" => "required",
    //         "QtyPR" => "required",
    //         "Satuan" => "required",
    //         "Satuan_Konversi" => "required",
    //         "KonversiQty" => "required",
    //         "Konversi_QtyTotal" => "required",
    //         "UserAdd" => "required"
    //     ]);
    //     try {
    //         // Db Transaction
    //         DB::beginTransaction(); 

    //         // cek ada gak datanya
    //         if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
    //             return $this->sendError('Sales Transaction Number Not Found !', []);
    //         }
    //         // cek kode barangnya ada ga
    //         if($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1){
    //             return $this->sendError('Product Not Found !', []);
    //         }
    //         // cek Konversi nya udah belom
    //         $konversi = $this->aBarangRepository->getBarangbyId($request->ProductCode)->first();
    //         if ($konversi->Konversi_satuan  < 1) {
    //             return $this->sendError('Konversi Satuan Invalid, Silahkan Masukan Konversi Satuan !', []);
    //         }
    //         //cek barang dobel gak 
    //         if($this->aPurchaseRequisitionRepository->getItemsDouble($request)->count() > 0 ){
    //             return $this->sendError('Product Code Double !', []);
    //         }
           
    //         $this->aPurchaseRequisitionRepository->addPurchaseRequisitionDetil($request);
            
    //         DB::commit();
    //         return $this->sendResponse([], 'Items Purchase Requisition Add Successfully !');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::info($e->getMessage());
    //         return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
    //     }

    // }
    public function getConsumableChargedPeriode($request)
    {
        // validate 
        $request->validate([
            "tglPeriodeAwal" => "required",
            "tglPeriodeAkhir" => "required"
        ]);
        
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getConsumableChargedPeriode($request);
            // // cek ada gak datanya
            if ($this->aSalesRepository->getConsumableChargedPeriode($request)->count() < 1) {
                return $this->sendError('Sales Transaction Number Not Found !', []);
            }
            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }
    public function getSalesDetailbyNoReg($request)
    {
        // validate 
        $request->validate([
            "NoRegistrasi" => "required",
            "UnitCode" => "required",
            "UnitSales" => "required",
        ]);
        // // cek ada gak datanya
        if ($this->aSalesRepository->getSalesDetailbyNoReg($request)->count() < 1) {
            return $this->sendError('Sales Transaction Number Detil Not Found !', []);
        }
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesDetailbyNoReg($request);
            if ($data->count() < 1) {
                return $this->sendError('Sales Transaction Number Detil Not Found !', []);
            }
            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }

    public function getSalesbyPeriodeResep($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required"
        ]);
        
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesbyPeriodeResep($request);

            // // cek ada gak datanya
            if ($this->aSalesRepository->getSalesbyPeriodeResep($request)->count() < 1) {
                return $this->sendError('Sales Transaction Number Not Found !', []);
            }

            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }

    public function getSalesbyIDandNoResep($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "NoResep" => "required",
        ]);
        // // cek ada gak datanya
        if ($this->aSalesRepository->getSalesbyIDandNoResep($request->TransactionCode,$request->NoResep)->count() < 1) {
            return $this->sendError('Sales Transaction Number Detil Not Found !', []);
        }
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesbyIDandNoResep($request->TransactionCode,$request->NoResep);
            if ($data->count() < 1) {
                return $this->sendError('Sales Transaction Number Detil Not Found !', []);
            }
            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }

    public function getSalesDetailbyIDandNoResep($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "NoResep" => "required",
        ]);
        // // cek ada gak datanya
        if ($this->aSalesRepository->getSalesDetailbyIDandNoResep($request->TransactionCode,$request->NoResep)->count() < 1) {
            return $this->sendError('Sales Transaction Number Detil Not Found !', []);
        }
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesDetailbyIDandNoResep($request->TransactionCode,$request->NoResep);
            if ($data->count() < 1) {
                return $this->sendError('Sales Transaction Number Detil Not Found !', []);
            }
            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }

    public function getSalesbyPeriodeTanpaResep($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required"
        ]);
        
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesbyPeriodeTanpaResep($request);

            // // cek ada gak datanya
            if ($this->aSalesRepository->getSalesbyPeriodeTanpaResep($request)->count() < 1) {
                return $this->sendError('Sales Transaction Number Not Found !', []);
            }

            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }

    public function voidSalesTebus(Request $request)
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
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Transaksi Penjualan tidak ditemukan !', []);
        }
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
        }

        //cek jika sudah diapprove atau belum
        if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->Group_Transaksi == 'RESEP' || $this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->Group_Transaksi== 'NON RESEP'){
            if ($this->billingRepository->getBillingFo($request)->count() > 0){
                return $this->sendError('Order Sudah Diapprove ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }
                
        //cek jika billing sudah ada payment atau belum
        $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
        if ($cekbill > 0){        
            if ($this->billingRepository->getBilling1byTrsID($request->TransactionCode)->first()->ID_TRS_Payment != null){
                return $this->sendError('Billing Sudah Ada Payment ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
            } 
        }

        $noreg = $this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->NoRegistrasi;
        if ($noreg != 0 || $noreg != null){
            $noregpass = $noreg;
        }else{
            $noregpass = $request->TransactionCode;
        }
        //cek jika billing sudah diclose atau belum
        if ($this->billingRepository->getBillingClose($noregpass)->count() > 0){
            return $this->sendError('Billing Sudah Diclose ! Silahkan Konfirmasi Ke Bagian Kasir !', []); 
        } 

        if ($this->returJualRepository->getReturJualDetailbySalesCode($request->TransactionCode)->count() > 0) {
            return $this->sendError('Sales Number Sudah Pernah Ada Riwayat Transaksi Retur ! Silahkan Dicek Kembali !', []);
        }


        try {
            // Db Transaction
            DB::beginTransaction(); 
            $reff_void = 'TPR_V';
            // Load Data All Do Detil Untuk Di Looping 
            $dtlconsumable = $this->aSalesRepository->getSalesDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $key2) {
                // $QtyPakai = $key2->Qty;
                // $Konversi_QtyTotal = $key2->Konversi_QtyTotal;

                $cekqtystok = $this->sStokRepository->cekStokbyIDBarangOnly($key2->ProductCode,$request);
 
                    if ( $cekqtystok->count() < 1) {
                        return  $this->sendError('Qty Stok '.$key2->ProductName.' Tidak ada diLayanan Tujuan Ini ! ' , []);
                    }
                    // foreach ($cekqtystok as $valueStok) {
                    //     $datastok = $valueStok->Qty;
                    // } 
                // $sisaStok = $datastok + $Konversi_QtyTotal;
                // $this->sStokRepository->updateStokPerItemBarang($request, $key2->ProductCode, $sisaStok);

                // buku 
                    $cekBuku = $this->sStokRepository->cekBukuByTransactionandCodeProduct($key2->ProductCode,$request,'TPR');
                    foreach ($cekBuku as $data) {
                       $asd = $data;
                    }
                    
                    $this->sStokRepository->addBukuStokInVoidFromSelect($asd,$reff_void,$request);
                    $this->sStokRepository->addDataStoksInVoidFromSelect($asd,$reff_void,$request);
            }

            
            //update qty realisasi
            //if ($this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->Group_Transaksi == 'RESEP'){
                $noresep = $this->aSalesRepository->getSalesbyID($request->TransactionCode)->first()->NoResep;
                $getiter = $this->trsResepRepository->viewOrderResepbyOrderIDV2($noresep)->first()->IterRealisasi;
                //tambahan 05-11-2024 code:05112024 
                if ($getiter == null || $getiter == 0){
                    $getiter = 0;
                }else{
                    $getiter -= 1;
                }
                $this->trsResepRepository->updateIterReal($noresep,$getiter);
              //}
        
            $this->aSalesRepository->voidSalesDetailAllOrder($request);
            $this->aSalesRepository->voidSales($request);


            // void billing transaction
            $this->billingRepository->voidBillingPasien($request);
            $this->billingRepository->voidBillingPasienOne($request);
            $this->billingRepository->voidBillingPasienTwo($request);

            $request['NoRegistrasi'] = $noregpass;
            //void registrasi
            $this->visitRepository->voidRegistrasivisit($request);
            $this->visitRepository->voidRegistrasidashboard($request);

            DB::commit();
            return $this->sendResponse([], 'Transaksi Penjualan berhasil dihapus !');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal !', $e->getMessage());
        }
    }

    //tambahan 30-10-2024 code:30102024 dan antrian
    public function getSalesbyPeriodeResepRajal($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required"
        ]);
        
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesbyPeriodeResepRajal($request);

            // // cek ada gak datanya
            if ($this->aSalesRepository->getSalesbyPeriodeResepRajal($request)->count() < 1) {
                return $this->sendError('Sales Transaction Number Not Found !', []);
            }

            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }

    
    //tambahan 05-11-2024 code:05112024 dan antrian
    public function getSalesbyPeriodeResepRanap($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required"
        ]);
        
        try {
            // Db Transaction
            $data = $this->aSalesRepository->getSalesbyPeriodeResepRanap($request);

            // // cek ada gak datanya
            if ($this->aSalesRepository->getSalesbyPeriodeResepRanap($request)->count() < 1) {
                return $this->sendError('Sales Transaction Number Not Found !', []);
            }

            return $this->sendResponse($data, 'Sales Transaction Data Found !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Sales Transaction Data Not Found !', $e->getMessage());
        }
    }
}