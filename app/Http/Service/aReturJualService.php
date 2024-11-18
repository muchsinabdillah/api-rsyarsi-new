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

class aReturJualService extends Controller
{
    use AutoNumberTrait;
    use FifoTrait;
    private $trsResepRepository;
    private $aDeliveryOrderRepository;
    private $aBarangRepository;
    private $asupplierRepository;
    private $aStok;
    private $ahnaRepository; 
    private $aMasterUnitRepository; 
    private $aSalesRepository; 
    private $visitRepository;
    private $billingRepository;
    private $returJualRepository; 

    public function __construct(
        aTrsResepRepositoryImpl  $trsResepRepository,
        aDeliveryOrderRepositoryImpl $aDeliveryOrderRepository,
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $asupplierRepository,
        aStokRepositoryImpl $aStok,    
        aHnaRepositoryImpl $ahnaRepository,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aSalesRepositoryImpl $aSalesRepository,
        bVisitRepositoryImpl $visitRepository,
        bBillingRepositoryImpl $billingRepository,
        aReturJualRepositoryImpl $returJualRepository 
    ) {
        $this->trsResepRepository = $trsResepRepository;
        $this->aDeliveryOrderRepository = $aDeliveryOrderRepository;
        $this->aBarangRepository = $aBarangRepository;
        $this->asupplierRepository = $asupplierRepository;
        $this->aStok = $aStok;
        $this->ahnaRepository = $ahnaRepository;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->aSalesRepository = $aSalesRepository;
        $this->visitRepository = $visitRepository;
        $this->billingRepository = $billingRepository;
        $this->returJualRepository = $returJualRepository;
    }
    public function addReturJualHeader(Request $request)
    {
         // validate 
         $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitCode" => "required", 
            "UnitSales" => "required", 
            "SalesCode" => "required", 
            "NoResep" => "required", 
            "Group_Transaksi" => "required",  
            "NoRegistrasi" => "required",  
            "Notes" => "required" ,
        ]);

        
        try {
            // Db Transaction
            DB::beginTransaction(); 

            //cek reg nya udah close belom
            if(substr($request->NoRegistrasi,0,2) == "RJ"  ) {
                $getReg = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
            }else{
                $getReg = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
            }

            if ($getReg->StatusID == '4') {
                return $this->sendError('Registrasi Sudah Close. TIdak bisa Input Data Retur Jual !', []);
            }

            if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
                return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
            }
            
            // //Cek Di table OrderResep
            if($request->Group_Transaksi == "RESEP"){
                if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->count() < 1) {
                    return $this->sendError('No. Resep Dokter tidak ditemukan !', []);
                }
            } 

            if ($this->aSalesRepository->getSalesbyID($request->SalesCode)->count() < 1) {
                return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
            }

            $getmax = $this->returJualRepository->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }

            $autonumber = $this->ReturJual($request, $TransactionCode);

            $this->returJualRepository->addReturJualHeader($request, $autonumber);

            DB::commit();
            return $this->sendResponse($autonumber, 'Transaksi Retur Jual berhasil ditambahkan !');
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addReturJualFinish(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitCode" => "required", 
            "SalesCode" => "required", 
            "NoResep" => "required", 
            "Group_Transaksi" => "required",  
            "NoRegistrasi" => "required",  
            "Notes" => "required" ,
            "TotalQtyReturJual" => "required" ,
            "TotalQtysales" => "required" ,
            "TotalRow" => "required" ,
        ]); 
        try {
            // Db Transaction
            DB::beginTransaction();

            // validasi 
            // // cek ada gak datanya
            if ($this->returJualRepository->getRejualHeaderbyId($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Retur Jual tidak ditemukan !', []);
            }

            //cek reg nya udah close belom
            if(substr($request->NoRegistrasi,0,2) == "RJ"  ) {
                $getReg = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
            }else{
                $getReg = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
            }
            if ($getReg->StatusID == '4') {
                return $this->sendError('Registrasi Sudah Close. TIdak bisa Input Data Retur Jual !', []);
            }

            if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
                return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
            }
            
            // //Cek Di table OrderResep
            if($request->Group_Transaksi == "RESEP"){
                if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->count() < 1) {
                    return $this->sendError('No. Resep Dokter tidak ditemukan !', []);
                }
            } 

            if ($this->aSalesRepository->getSalesbyID($request->SalesCode)->count() < 1) {
                return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
            }


            // validasi Kode
            foreach ($request->Items as $key) {
                # code...
                // // cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                    return $this->sendError('Kode Barang tidak ditemukan !', []);
                }
            }
            

            foreach ($request->Items as $key) {
                // Update Purchase Order Qty Remain 
                $getDetailData = $this->returJualRepository->getReturJualDetailbyIDBarang($request, $key);
                $datasalesDetails = $this->aSalesRepository->getSalesDetailbyIDBarangFix($request->SalesCode,$key['ProductCode'])->first();

                
              
                if($getDetailData->count() < 1){ 
                    if ($key['QtyReturJual'] > 0) {
                        $this->returJualRepository->addReturJualDetail($request,$key);
                        $QtyRemain = $datasalesDetails->QtySalesRemain; 
                        $QtyAfter = $QtyRemain - $key['QtyReturJual'];
                        $this->aSalesRepository->updateQtRemainSalesDetail($request->SalesCode,$key['ProductCode'], $QtyAfter);
                        
                        $this->fifoReturJual($request,$key,'TRJ');
                    }
                }else{
                    // jika sudah ada
                    $showData = $getDetailData->first();
                    $mtKonversi_QtyTotal = $showData->QtyReturJual;
                    $QtyReturNew = $showData->QtyReturJual;
                    if($mtKonversi_QtyTotal <> $key['Konversi_QtyTotal']){ // Dirubah jika Qty nya ada Perubahan Aja

                        // update qty Jual untuk qty remain
                        $SalesRemain = $datasalesDetails->QtySalesRemain;  
                        $QtyRemain = $SalesRemain+$QtyReturNew;
                        $doqtyAfter = $QtyRemain - $key['QtyReturJual'];
                        $this->aSalesRepository->updateQtRemainSalesDetail($request->SalesCode,$key['ProductCode'], $doqtyAfter);
                        $this->aStok->deleteBukuStok($request,$key,"TRJ",$request->UnitCode);  
                        $this->aStok->deleteDataStoks($request,$key,"TRJ",$request->UnitCode); 
                        $this->fifoReturJual($request,$key,'TRJ');

                    }
                } 

                $this->returJualRepository->editReturJualDetailbyIdBarang($request,$key);
            }

            // update tabel header
            $this->returJualRepository->editReturJualHeader($request);

            // BILLING
            $tipereg = substr($request->NoRegistrasi, 0, 2);
                if ($tipereg == 'RJ'){
                    $getdataregpasien = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
                }elseif($tipereg == 'RI'){
                    $getdataregpasien = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
                    $request['KodeKelas'] = $getdataregpasien->KelasID_Akhir;
                }else{
                    return  $this->sendError('Nomor Registrasi Tidak Valid ! ' , []);
                }
                $getdatasaleshdr = $this->aSalesRepository->getSalesbyID($request->SalesCode)->first();
                $request['TotalSales'] = $request->TotalReturJualRp*-1;
                $request['SubtotalQtyPrice'] = $request->TotalQtyReturJual*-1;
                $request['Discount_Prosen'] = $getdatasaleshdr->Discount;
                $request['Discount'] = 0;
                $request['Subtotal'] = $request->TotalReturJualRp*-1; 
                $request['Grandtotal'] = $getdatasaleshdr->Grandtotal*-1;
                $request['NoMr'] = $getdataregpasien->NoMR;
                $request['NoEpisode'] = $getdataregpasien->NoEpisode;
                $request['GroupJaminan'] = $getdataregpasien->TipePasien;
                $request['KodeJaminan'] = $getdataregpasien->KodeJaminan;
                $request['IdUnit'] = $getdataregpasien->IdUnit;
                $request['UnitTujuan'] = $request->UnitSales;
                $request['TotalQtyOrder'] = $request->TotalQtysales*-1;
                
            // // insert ke billing header
            $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
            //cek jika sudah ada di table
            if ( $cekbill > 0) {
                //update
                $this->billingRepository->updateHeader($request,$request->TransactionCode);
            }else{
                //insert
                $this->billingRepository->insertHeader($request,$request->TransactionCode);
            }

            foreach ($request->Items as $key) {
                $qtyretur = $key['QtyReturJual']*-1;
                $hargaretur = $key['ReturPrice']*-1;
                $totalretur = $key['TotalReturJual']*-1;
                if ($cekbill > 0 ){
                    //update billing detail
                    $this->billingRepository->updateDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                    $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                    $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                    'Farmasi',$request->KodeKelas,$qtyretur,$hargaretur,$totalretur,
                    0,0,$totalretur,$totalretur,'','','','FARMASI');
                }else{
                    // insert billing detail
                   $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                   $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                   $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                   'Farmasi',$request->KodeKelas,$qtyretur,$hargaretur,$totalretur,
                   0,0,$totalretur,$totalretur,'','','','FARMASI');
                }
            }

            //inser billing pdp
            $dataBilling1 = $this->billingRepository->getBillingFo1($request);
            foreach ($dataBilling1 as $dataBilling1) {
                $this->billingRepository->insertDetailPdp($dataBilling1);
            } 
            //#END BILLING

            DB::commit();
            return $this->sendResponse([], 'Retur Jual berhasil di Simpan !');
         } catch (Exception $e) {
             DB::rollBack();
             Log::info($e->getMessage());
             return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
         }
    }
    public function voidReturJualDetailbyItem(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required", 
            "ProductCode" => "required",
            "UnitCode" => "required", 
            "NoRegistrasi" => "required",  
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();


            // validasi 
            // // cek ada gak datanya
            if ($this->returJualRepository->getRejualHeaderbyId($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Retur Jual tidak ditemukan !', []);
            }

            //cek reg nya udah close belom
            if(substr($request->NoRegistrasi,0,2) == "RJ"  ) {
                $getReg = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
            }else{
                $getReg = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
            }
            if ($getReg->StatusID == '4') {
                return $this->sendError('Registrasi Sudah Close. Tidak bisa Input Data Retur Jual !', []);
            }

            if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
                return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
            }
            
            if ($this->aSalesRepository->getSalesbyID($request->SalesCode)->count() < 1) {
                return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
            }

            // validasi Kode
            // cek kode unit ini bener ga atas transaksi ini
            $cekdodetil = $this->returJualRepository->getReturJualbyIDUnitOrder($request)->count();
            if ($cekdodetil < 1) {
                return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
            }

            // BARANG - cek kode barangnya ada ga
            if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
                return $this->sendError('Kode Barang tidak ditemukan !', []);
            } 

            // BARANG - cek aktif engga
            $cekdodetil = $this->returJualRepository->getReturJualDetailbyIDandProductCode($request)->count();
            if ($cekdodetil < 1) {
                return $this->sendError('Kode Barang Sudah di Batalkan !', []);
            }

            // BARANG - cek Stok
            $cekqtystok = $this->aStok->cekStokbyIDBarangOnlyMutasi($request->ProductCode,$request->UnitCode)->first();
 
            $stokCurrent = (float)$cekqtystok->Qty;
            if ($stokCurrent < $request->Konversi_QtyTotal) {
                return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $request->Konversi_QtyTotal . ' ! ', []);
            }
            
            // BARANG - cek aktif engga getMutasiDetailbyID
            $cekdodetil = $this->returJualRepository->getReturJualDetailbyID($request->TransactionCode)->count();
            if ($cekdodetil <= 1) {
                return $this->sendError('Kode Barang atas Transaksi Mutasi ini Hanya 1, Silahkan Hapus semua Transaksi Retur Jual ini !', []);
            }

            // TRANSAKSI DISINI
            $dtlReturJual = $this->returJualRepository->getReturJualDetailbyIDandProductCode($request)->first();
            $Konversi_QtyTotal = $dtlReturJual->Konversi_Qty_Total;
            $QtyReturJual = $dtlReturJual->QtyReturJual;

            $datasalesDetails = $this->aSalesRepository->getSalesDetailbyIDBarangFix($request->SalesCode,$request->ProductCode)->first();
            $QtyRemain = $datasalesDetails->QtySalesRemain; 
            $QtyRemainFinal = $QtyReturJual + $QtyRemain;
            $this->aSalesRepository->updateQtRemainSalesDetail($request->SalesCode,$request->ProductCode, $QtyRemainFinal);
            
            $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($request->ProductCode,$request,'TRJ');
                foreach ($cekBuku as $data) {
                    $asd = $data;
                } 
                $this->aStok->addBukuStokOutVoidFromSelectMutasi($asd,'TRJ_V',$request,$Konversi_QtyTotal,$request->UnitCode);
                $this->aStok->addDataStoksOutVoidFromSelectMutasi($asd,'TRJ_V',$request,$Konversi_QtyTotal,$request->UnitCode);
                
            $this->returJualRepository->voidReturJualDetailbyItemAll($request,$request->ProductCode);
            
            //void billing
            $this->billingRepository->voidBillingPasienOneByProductCode($request);
            $this->billingRepository->voidBillingPasienTwoByProductCode($request);
            //update billing header
            $getDataSumBill1 = $this->billingRepository->getSumBillingFo1($request);
            if ($getDataSumBill1->count() > 0){
                $this->billingRepository->updateSumBillingHeader($request->TransactionCode,$getDataSumBill1->first());
            }


            $sumReturjualdetil = $this->returJualRepository->getSumReturJualDetil($request->TransactionCode)->first(); 

            $dtlReturJual = $this->returJualRepository->updateSumReturHeader($request->TransactionCode,$sumReturjualdetil->QtyReturJual,
                                                    $sumReturjualdetil->QtySales,$sumReturjualdetil->TotalRow,
                                                    $sumReturjualdetil->TotalReturJual,$request->UserVoid);

            DB::commit();
            return $this->sendResponse([], 'Retur Jual per Barang berhasil di Hapus !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }

    }
    public function voidReturJual(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required",  
            "SalesCode" => "required",
            "UnitCode" => "required",
            "DateVoid" => "required",
            "ReasonVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // validasi 
            // // cek ada gak datanya
            if ($this->returJualRepository->getRejualHeaderbyId($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Retur Jual tidak ditemukan !', []);
            }

            //cek reg nya udah close belom
            if(substr($request->NoRegistrasi,0,2) == "RJ"  ) {
                $getReg = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
            }else{
                $getReg = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
            }
            if ($getReg->StatusID == '4') {
                return $this->sendError('Registrasi Sudah Close. Tidak bisa Input Data Retur Jual !', []);
            }

            if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
                return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
            }
            
            if ($this->aSalesRepository->getSalesbyID($request->SalesCode)->count() < 1) {
                return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
            }

            // validasi Kode
            // cek kode unit ini bener ga atas transaksi ini
            $cekdodetil = $this->returJualRepository->getReturJualbyIDUnitOrder($request)->count();
            if ($cekdodetil < 1) {
                return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
            }

            // VALIDASI BY BARANG
            $dtlconsumable = $this->returJualRepository->getReturJualDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $keyDetil) {
                 // BARANG - cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($keyDetil->ProductCode)->count() < 1) {
                    return $this->sendError('Kode Barang tidak ditemukan !', []);
                }  

                // BARANG - cek Stok
                $cekqtystok = $this->aStok->cekStokbyIDBarangOnlyMutasi($keyDetil->ProductCode,$request->UnitCode)->first();
    
                $stokCurrent = (float)$cekqtystok->Qty;
                if ($stokCurrent < $request->Konversi_QtyTotal) {
                    return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $request->Konversi_QtyTotal . ' ! ', []);
                } 
            }

            $dtlReturjual = $this->returJualRepository->getReturJualDetailbyID($request->TransactionCode);
            foreach ($dtlReturjual as $key2) {
                $Konversi_QtyTotal = $key2->Konversi_Qty_Total;
                $QtyReturJual = $key2->QtyReturJual;
                $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($key2->ProductCode,$key2,'TRJ');
                foreach ($cekBuku as $data) {
                    $asd = $data;
                } 
                $this->aStok->addBukuStokOutVoidFromSelectMutasi($asd,'TRJ_V',$request,$Konversi_QtyTotal, $request->UnitCode);
                $this->aStok->addDataStoksOutVoidFromSelectMutasi($asd,'TRJ_V',$request,$Konversi_QtyTotal, $request->UnitCode);
                
                $datasalesDetails = $this->aSalesRepository->getSalesDetailbyIDBarangFix($key2->SalesCode,$key2->ProductCode)->first();
                $QtyRemain = $datasalesDetails->QtySalesRemain; 
                $QtyRemainFinal = $QtyReturJual + $QtyRemain;
                $this->aSalesRepository->updateQtRemainSalesDetail($key2->SalesCode,$key2->ProductCode, $QtyRemainFinal);
                $this->returJualRepository->voidReturJualDetailbyItemAll($request,$key2->ProductCode);
            }

            
            
            // void billing transaction
            $this->billingRepository->voidBillingPasien($request);
            $this->billingRepository->voidBillingPasienOne($request);
            $this->billingRepository->voidBillingPasienTwo($request);
            
            $this->returJualRepository->voidReturJual($request);
            DB::commit();
            return $this->sendResponse([], 'Retur Jual berhasil di Hapus !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }

    }
    public function getReturJualbyID(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);
        try {
            // cek ada gak datanya
            if ($this->returJualRepository->getRejualHeaderbyId($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Tranasksi Retur Jual tidak ditemukan !', []);
            }
            $data = $this->returJualRepository->getRejualHeaderbyId($request->TransactionCode);
            return $this->sendResponse($data, 'No. Tranasksi Retur Jual ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Tranasksi Retur Jual tidak ditemukan !', $e->getMessage());
        }
    }
    public function getReturJualDetailbyID(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);

        try {
            // cek ada gak datanya
            if ($this->returJualRepository->getRejualHeaderbyId($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Tranasksi Retur Jual tidak ditemukan !', []);
            }
            $data = $this->returJualRepository->getReturJualDetailbyID($request->TransactionCode);
            return $this->sendResponse($data, 'No. Tranasksi Retur Jual ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Tranasksi Retur Jual tidak ditemukan !', $e->getMessage());
        }
    }
    public function getReturJualbyDateUser(Request $request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);
        try {
            $data = $this->returJualRepository->getReturJualbyDateUser($request);
            return $this->sendResponse($data, 'No. Transaksi ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Transaksi tidak ditemukan !', $e->getMessage());
        }
    }
    public function getReturJualbyPeriode(Request $request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);
        try {
            $data = $this->returJualRepository->getReturJualbyPeriode($request);
            return $this->sendResponse($data, 'No. Transaksi ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Transaksi tidak ditemukan !', $e->getMessage());
        }
    }
    public function addReturJualHeaderbyNoReg(Request $request)
    {
         // validate 
         $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitCode" => "required", 
            "UnitSales" => "required", 
            //"SalesCode" => "required", 
            //"NoResep" => "required", 
            //"Group_Transaksi" => "required",  
            "NoRegistrasi" => "required",  
            "Notes" => "required" ,
        ]);

        
        try {
            // Db Transaction
            DB::beginTransaction(); 

            //cek reg nya udah close belom
            if(substr($request->NoRegistrasi,0,2) == "RJ"  ) {
                $getReg = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
            }else{
                $getReg = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
            }

            if ($getReg->StatusID == '4') {
                return $this->sendError('Registrasi Sudah Close. TIdak bisa Input Data Retur Jual !', []);
            }

            if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
                return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
            }
            
            // //Cek Di table OrderResep
            // if($request->Group_Transaksi == "RESEP"){
            //     if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->count() < 1) {
            //         return $this->sendError('No. Resep Dokter tidak ditemukan !', []);
            //     }
            // } 
            
            if ($this->aSalesRepository->getSalesDetailbyNoReg($request)->count() < 1) {
                return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
            }

            $getmax = $this->returJualRepository->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }

            $autonumber = $this->ReturJual($request, $TransactionCode);

            $this->returJualRepository->addReturJualHeader($request, $autonumber);

            DB::commit();
            return $this->sendResponse($autonumber, 'Transaksi Retur Jual berhasil ditambahkan !');
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }

    public function addReturJualFinishbyReg(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitCode" => "required", 
            //"SalesCode" => "required", 
            //"NoResep" => "required", 
            "Group_Transaksi" => "required",  
            "NoRegistrasi" => "required",  
            "Notes" => "required" ,
            "TotalQtyReturJual" => "required" ,
            "TotalQtysales" => "required" ,
            "TotalRow" => "required" ,
        ]); 
        try {
            // Db Transaction
            DB::beginTransaction();

            // validasi 
            // // cek ada gak datanya
            if ($this->returJualRepository->getRejualHeaderbyId($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Retur Jual tidak ditemukan !', []);
            }

            //cek reg nya udah close belom
            if(substr($request->NoRegistrasi,0,2) == "RJ"  ) {
                $getReg = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
            }else{
                $getReg = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
            }
            if ($getReg->StatusID == '4') {
                return $this->sendError('Registrasi Sudah Close. Tidak bisa Input Data Retur Jual !', []);
            }

            if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
                return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
            }
            
            // //Cek Di table OrderResep
            // if($request->Group_Transaksi == "RESEP"){
            //     if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->count() < 1) {
            //         return $this->sendError('No. Resep Dokter tidak ditemukan !', []);
            //     }
            // } 

            // if ($this->aSalesRepository->getSalesbyID($request->SalesCode)->count() < 1) {
            //     return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
            // }


            // validasi Kode
            foreach ($request->Items as $key) {
                # code...
                // // cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                    return $this->sendError('Kode Barang tidak ditemukan !', []);
                }
            }
            
            foreach ($request->Items as $key) {
                
                //get props data
                // $getdatadetailbyid = $this->aSalesRepository->getSalesDetailbyIDDetail($key['IDDetail'])->first();
                // $request['SalesCode'] = $getdatadetailbyid->TransactionCode;
                $request['SalesCode'] = $key['SalesCode'];
                
                // Update Purchase Order Qty Remain 
                $getDetailData = $this->returJualRepository->getReturJualDetailbyIDBarang($request, $key);
                $datasalesDetails = $this->aSalesRepository->getSalesDetailbyIDBarangFix($key['SalesCode'],$key['ProductCode'])->first();

                
              
                if($getDetailData->count() < 1){ 
                    if ($key['QtyReturJual'] > 0) {
                        $this->returJualRepository->addReturJualDetail($request,$key);
                        $QtyRemain = $datasalesDetails->QtySalesRemain; 
                        $QtyAfter = $QtyRemain - $key['QtyReturJual'];
                        $this->aSalesRepository->updateQtRemainSalesDetail($key['SalesCode'],$key['ProductCode'], $QtyAfter);
                        
                        $this->fifoReturJual($request,$key,'TRJ');
                    }
                }else{
                    // jika sudah ada
                    $showData = $getDetailData->first();
                    $mtKonversi_QtyTotal = $showData->QtyReturJual;
                    $QtyReturNew = $showData->QtyReturJual;
                    if($mtKonversi_QtyTotal <> $key['Konversi_QtyTotal']){ // Dirubah jika Qty nya ada Perubahan Aja

                        // update qty Jual untuk qty remain
                        $SalesRemain = $datasalesDetails->QtySalesRemain;  
                        $QtyRemain = $SalesRemain+$QtyReturNew;
                        $doqtyAfter = $QtyRemain - $key['QtyReturJual'];
                        $this->aSalesRepository->updateQtRemainSalesDetail($key['SalesCode'],$key['ProductCode'], $doqtyAfter);
                        $this->aStok->deleteBukuStok($request,$key,"TRJ",$request->UnitCode);  
                        $this->aStok->deleteDataStoks($request,$key,"TRJ",$request->UnitCode); 
                        $this->fifoReturJual($request,$key,'TRJ');

                    }
                } 

                $this->returJualRepository->editReturJualDetailbyIdBarang($request,$key);
            }

            // update tabel header
            $this->returJualRepository->editReturJualHeader($request);

            // BILLING
            $tipereg = substr($request->NoRegistrasi, 0, 2);
                if ($tipereg == 'RJ'){
                    $getdataregpasien = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
                }elseif($tipereg == 'RI'){
                    $getdataregpasien = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
                    $request['KodeKelas'] = $getdataregpasien->KelasID_Akhir;
                }else{
                    return  $this->sendError('Nomor Registrasi Tidak Valid ! ' , []);
                }
                //$getdatasaleshdr = $this->aSalesRepository->getSalesbyID($request->SalesCode)->first();
                $request['TotalSales'] = $request->TotalReturJualRp*-1;
                $request['SubtotalQtyPrice'] = $request->TotalQtyReturJual*-1;
                //$request['Discount_Prosen'] = $getdatasaleshdr->Discount;
                $request['Discount_Prosen'] = 0;
                $request['Discount'] = 0;
                $request['Subtotal'] = $request->TotalReturJualRp*-1; 
                //$request['Grandtotal'] = $getdatasaleshdr->Grandtotal*-1;
                $request['Grandtotal'] = $request->TotalReturJualRp*-1; 
                $request['NoMr'] = $getdataregpasien->NoMR;
                $request['NoEpisode'] = $getdataregpasien->NoEpisode;
                $request['GroupJaminan'] = $getdataregpasien->TipePasien;
                $request['KodeJaminan'] = $getdataregpasien->KodeJaminan;
                $request['IdUnit'] = $getdataregpasien->IdUnit;
                $request['UnitTujuan'] = $request->UnitSales;
                $request['TotalQtyOrder'] = $request->TotalQtysales*-1;
                
            // // insert ke billing header
            $cekbill = $this->billingRepository->getBillingFo($request)->count(); 
            //cek jika sudah ada di table
            if ( $cekbill > 0) {
                //update
                $this->billingRepository->updateHeader($request,$request->TransactionCode);
            }else{
                //insert
                $this->billingRepository->insertHeader($request,$request->TransactionCode);
            }

            foreach ($request->Items as $key) {
                //$cekbill = $this->billingRepository->getBillingFo($request)->count();
                $qtyretur = $key['QtyReturJual']*-1;
                $hargaretur = $key['ReturPrice']*-1;
                $totalretur = $key['TotalReturJual']*-1;
                if ($cekbill > 0 ){
                    //update billing detail
                    $this->billingRepository->updateDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                    $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                    $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                    'Farmasi',$request->KodeKelas,$qtyretur,$hargaretur,$totalretur,
                    0,0,$totalretur,$totalretur,'','','','FARMASI');
                }else{
                    // insert billing detail
                   $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                   $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                   $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                   'Farmasi',$request->KodeKelas,$qtyretur,$hargaretur,$totalretur,
                   0,0,$totalretur,$totalretur,'','','','FARMASI');
                }
            }

            //inser billing pdp
            $dataBilling1 = $this->billingRepository->getBillingFo1($request);
            foreach ($dataBilling1 as $dataBilling1) {
                $this->billingRepository->insertDetailPdp($dataBilling1);
            } 
            //#END BILLING

            DB::commit();
            return $this->sendResponse([], 'Retur Jual berhasil di Simpan !');
         } catch (Exception $e) {
             DB::rollBack();
             Log::info($e->getMessage());
             return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
         }
    }

    public function voidReturJualPerRegister(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required",  
            //"SalesCode" => "required",
            "UnitCode" => "required",
            "DateVoid" => "required",
            "ReasonVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // validasi 
            // // cek ada gak datanya
            if ($this->returJualRepository->getRejualHeaderbyId($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Retur Jual tidak ditemukan !', []);
            }

            //cek reg nya udah close belom
            if(substr($request->NoRegistrasi,0,2) == "RJ"  ) {
                $getReg = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi)->first();
            }else{
                $getReg = $this->visitRepository->getRegistrationRanapbyNoreg($request->NoRegistrasi)->first();
            }
            if ($getReg->StatusID == '4') {
                return $this->sendError('Registrasi Sudah Close. Tidak bisa Input Data Retur Jual !', []);
            }

            if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
                return $this->sendError('Kode Unit Penjualan tidak ditemukan !', []);
            }
            
            // if ($this->aSalesRepository->getSalesbyID($request->SalesCode)->count() < 1) {
            //     return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
            // }

            // validasi Kode
            // cek kode unit ini bener ga atas transaksi ini
            $cekdodetil = $this->returJualRepository->getReturJualbyIDUnitOrder($request)->count();
            if ($cekdodetil < 1) {
                return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
            }

            // VALIDASI BY BARANG
            $dtlconsumable = $this->returJualRepository->getReturJualDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $keyDetil) {

                if ($this->aSalesRepository->getSalesbyID($keyDetil->SalesCode)->count() < 1) {
                    return $this->sendError('Kode Transaksi Penjualan tidak ditemukan !', []);
                }

                 // BARANG - cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($keyDetil->ProductCode)->count() < 1) {
                    return $this->sendError('Kode Barang tidak ditemukan !', []);
                }  

                // BARANG - cek Stok
                $cekqtystok = $this->aStok->cekStokbyIDBarangOnlyMutasi($keyDetil->ProductCode,$request->UnitCode)->first();
    
                $stokCurrent = (float)$cekqtystok->Qty;
                if ($stokCurrent < $request->Konversi_QtyTotal) {
                    return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $request->Konversi_QtyTotal . ' ! ', []);
                } 
            }

            $dtlReturjual = $this->returJualRepository->getReturJualDetailbyID($request->TransactionCode);
            foreach ($dtlReturjual as $key2) {
                $Konversi_QtyTotal = $key2->Konversi_Qty_Total;
                $QtyReturJual = $key2->QtyReturJual;
                $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($key2->ProductCode,$key2,'TRJ');
                foreach ($cekBuku as $data) {
                    $asd = $data;
                } 
                $this->aStok->addBukuStokOutVoidFromSelectMutasi($asd,'TRJ_V',$request,$Konversi_QtyTotal, $request->UnitCode);
                $this->aStok->addDataStoksOutVoidFromSelectMutasi($asd,'TRJ_V',$request,$Konversi_QtyTotal, $request->UnitCode);
                
                $datasalesDetails = $this->aSalesRepository->getSalesDetailbyIDBarangFix($key2->SalesCode,$key2->ProductCode)->first();
                $QtyRemain = $datasalesDetails->QtySalesRemain; 
                $QtyRemainFinal = $QtyReturJual + $QtyRemain;
                $this->aSalesRepository->updateQtRemainSalesDetail($key2->SalesCode,$key2->ProductCode, $QtyRemainFinal);
                $this->returJualRepository->voidReturJualDetailbyItemAll($request,$key2->ProductCode);
            }

            
            
            // void billing transaction
            $this->billingRepository->voidBillingPasien($request);
            $this->billingRepository->voidBillingPasienOne($request);
            $this->billingRepository->voidBillingPasienTwo($request);
            
            $this->returJualRepository->voidReturJual($request);
            DB::commit();
            return $this->sendResponse([], 'Retur Jual berhasil di Hapus !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }

    }
}