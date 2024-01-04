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
use App\Http\Repository\aSalesRepositoryImpl;
use App\Http\Repository\bBillingRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;

class aSalesService extends Controller
{
    use AutoNumberTrait;
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
        bBillingRepositoryImpl $billingRepository
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
    }

    public function addSalesHeader(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitOrder" => "required", 
            "UnitTujuan" => "required", 
            "NoRegistrasi" => "required", 
            "Group_Transaksi" => "required", 
            "Notes" => "required" ,
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


            // //Cek Di table OrderResep
            // if($request->Group_Transaksi == "RESEP"){
            //     if ($this->trsResepRepository->viewOrderResepbyOrderIDV2($request->NoResep)->count() < 1) {
            //         return $this->sendError('No Resep Not Found !', []);
            //     }
            // }

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
            if ( $cekstok < 1) {
                return  $this->sendError('Qty Stok Tidak ada diLayanan Tujuan Ini ! ' , []);
            }
        }

        // validasi stok cukup engga
        foreach ($request->Items as $key) {
            # code...
             //  KHUSUS PAKAI BARANG CUKUP SATUAN TERKECIL LANGSUNG AJA.
            $cekstok = $this->sStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
            $getdatadetilmutasi = $this->aSalesRepository->getSalesDetailbyIDBarang($request, $key);
            $vGetMutasiDetil =  $getdatadetilmutasi->first();
             
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


        try {
            // Db Transaction
            DB::beginTransaction(); 

            // // billinga
            $this->billingRepository->insertHeader($request,$request->TransactionCode);

            foreach ($request->Items as $key) {
                $getdatadetilmutasi = $this->aSalesRepository->getSalesDetailbyIDBarang($request,$key);
                    // get Hpp Average 
                    $getHppBarang = $this->aHnaRepository->getHppAverage($key)->first();
                    $xhpp = $getHppBarang[0]->NominalHpp;
                    // get Hpp Average
                if($getdatadetilmutasi->count() < 1){
                    if ($key['Qty'] > 0) {
                        $this->aSalesRepository->addSalesDetail($request, $key,$xhpp); 
                    }
                }else{
                    // jika sudah ada
                    $showData = $getdatadetilmutasi->first();
                   
                    $mtKonversi_QtyTotal = $showData->Qty;
                    $mtQtyMutasi = $showData->Qty;

                   // if($mtKonversi_QtyTotal <> $key['Qty']){ // Dirubah jika Qty nya ada Perubahan Aja
                        $goQtyMutasiSisaheaderBefore = $mtQtyMutasi + $key['Qty'];
                        $goQtyMutasiSisaheaderAfter = $goQtyMutasiSisaheaderBefore - $key['Qty'];

                        $goQtyMutasiSisaKovenrsiBefore = $mtKonversi_QtyTotal + $key['Qty'];
                        $goQtyMutasiSisaKovenrsiAfter = $goQtyMutasiSisaKovenrsiBefore - $key['Qty'];

                         $this->aSalesRepository->editSalesDetailbyIdBarang($request,$key,$xhpp);

                        // replace stok ke awal
                        $getCurrentStok = $this->sStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                        $totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                        $this->sStokRepository->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                        $this->sStokRepository->deleteBukuStok($request,$key,"CM",$request->UnitTujuan);
                   // }  
                }
                 
                        // QUERY PENGURANGAN STOK METODE FIFO
                        first:
                        $getStokFirst = $this->sStokRepository->getStokExpiredFirst($request, $key);
                     
                        //  return $getStokFirst;
                        $DeliveryCodex = $getStokFirst->DeliveryCode;
                        $qtyBuku = $getStokFirst->x;
                        $ExpiredDate = $getStokFirst->ExpiredDate;
                        $BatchNumber = $getStokFirst->BatchNumber;

                        if ($qtyBuku < $key['Qty']) {
                            $qtynew = $qtyBuku;
                            $persediaan = $qtynew * $xhpp;
                        } else {
                            $qtynew = $key['Qty'];
                            $persediaan = $qtynew * $xhpp;
                        }
                        $TipeTrs = "TPR";
                        // // INSERT BUKU IN DI LAYANAN GUDANG
                        $this->sStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);

                        // update stok Tujuan / Gudang 
                        if ($this->sStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->count() < 1) {
                            //kalo g ada insert
                            $this->sStokRepository->addStokTrs($request, $key, $qtynew, $request->UnitTujuan);
                        } else {
                            //kallo ada ya update
                            $sumstok = $this->sStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan);
                            foreach ($sumstok as $value) {
                                $QtyCurrent = $value->Qty;
                            }
                            $QtyTotal = $QtyCurrent - $qtynew;
                            $this->sStokRepository->updateStokTrs($request, $key, $QtyTotal, $request->UnitTujuan);
                        }

                        if ($qtynew < $key['Qty']) {
                            $key['Qty'] = $key['Qty'] - $qtynew;
                            goto first;
                        }
                        // QUERY PENGURANGAN STOK METODE FIFO

                        //UPDATE SIGNA TERJEMAHAN
                        if ($key['Racik'] <> 0 ){
                            if ($key['RacikHeader'] == 1){
                                $this->trsResepRepository->editSignaTerjemahanbyID($key['IDResepDetail'],$key['AturanPakai']);
                            }
                        }else{
                            $this->trsResepRepository->editSignaTerjemahanbyID($key['IDResepDetail'],$key['AturanPakai']);
                        }
                
                    // insert billing detail
                    $this->billingRepository->insertDetail($request->TransactionCode,$request->TransactionDate,$request->UserCreate,
                                            $request->NoMr,$request->NoEpisode,$request->NoRegistrasi,$key['ProductCode'],
                                            $request->UnitTujuan,$request->GroupJaminan,$request->KodeJaminan,$key['ProductName'],
                                            'Farmasi',$request->KodeKelas,$key['Qty'],$key['Harga'],$key['SubtotalHarga'],
                                            $key['DiscountProsen'],$key['Discount'],$key['Subtotal'],$key['Grandtotal'],'','','','FARMASI');
            }
                // // update tabel header
                // $this->aSalesRepository->editSales($request);

                $dataBilling1 = $this->billingRepository->getBillingFo1($request);
                foreach ($dataBilling1 as $dataBilling1) {
                    $this->billingRepository->insertDetailPdp($dataBilling1);
                } 

                 //UPDATE REVIEW ORDER RESEP DETAILS
                 $this->trsResepRepository->editReviewbyIDResep($request->IdOrderResep);

                DB::commit();
                return $this->sendResponse([], 'Items Add Successfully !');
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
            return $this->sendError('Sales Transaction Number Not Found !', []);
        }
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Unit Order Code Not Found !', []);
        }


        try {
            // Db Transaction
            DB::beginTransaction(); 
            $reff_void = 'TPR_V';
            // Load Data All Do Detil Untuk Di Looping 
            $dtlconsumable = $this->aSalesRepository->getSalesDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $key2) {
                $QtyPakai = $key2->Qty;
                $Konversi_QtyTotal = $key2->Konversi_QtyTotal;

                $cekqtystok = $this->sStokRepository->cekStokbyIDBarangOnly($key2->ProductCode,$request);
 
                    if ( $cekqtystok->count() < 1) {
                        return  $this->sendError('Qty Stok Tidak ada diLayanan Tujuan Ini ! ' , []);
                    }
             

                
                    foreach ($cekqtystok as $valueStok) {
                        $datastok = $valueStok->Qty;
                    } 
                $sisaStok = $datastok + $Konversi_QtyTotal;
                $this->sStokRepository->updateStokPerItemBarang($request, $key2->ProductCode, $sisaStok);

                // buku 
                    $cekBuku = $this->sStokRepository->cekBukuByTransactionandCodeProduct($key2->ProductCode,$request,'TPR');
                    foreach ($cekBuku as $data) {
                       $asd = $data;
                    }
                    
                    $this->sStokRepository->addBukuStokInVoidFromSelect($asd,$reff_void,$request);
            }
        
            $this->aSalesRepository->voidSalesDetailAllOrder($request);
            $this->aSalesRepository->voidSales($request);

            // void billing transaction
            $this->billingRepository->voidBillingPasien($request);
            $this->billingRepository->voidBillingPasienOne($request);
            $this->billingRepository->voidBillingPasienTwo($request);

            DB::commit();
            return $this->sendResponse([], 'Transaction Resep Void Successfully !');
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
            return $this->sendError('Product Not Found !', []);
        } 
        // cek aktif engga
        $cekdodetil = $this->aSalesRepository->getSalesDetailbyIDandProductCode($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Barang Sudah di Batalkan !', []);
        }
        try {
            // Db Transaction
            DB::beginTransaction(); 

            $dtlDo = $this->aSalesRepository->getSalesDetailbyIDandProductCode($request)->first();
            $Konversi_QtyTotal = $dtlDo->Qty;

            $cekqtystok = $this->sStokRepository->cekStokbyIDBarangOnly($request->ProductCode,$request);
             
            foreach ($cekqtystok as $valueStok) {
                $datastok = $valueStok->Qty;
            } 
            $sisaStok = $datastok + $Konversi_QtyTotal;
            $this->sStokRepository->updateStokPerItemBarang($request, $request->ProductCode, $sisaStok);

            // buku 
            $cekBuku = $this->sStokRepository->cekBukuByTransactionandCodeProduct($request->ProductCode,$request,'TPR');
            foreach ($cekBuku as $data) {
               $asd = $data;
            } 
            $this->sStokRepository->addBukuStokInVoidFromSelect($asd,'TPR_V',$request);

            $this->aSalesRepository->voidSalesbyItem($request);

            DB::commit();
            return $this->sendResponse([], 'Transaction Sales Detil Void Successfully !');
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
            return $this->sendError('Sales Number Not Found !', []);
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


            DB::commit();
            return $this->sendResponse([], 'Transaction Sales Finish !');
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
}