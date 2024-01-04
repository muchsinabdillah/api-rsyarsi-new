<?php

namespace App\Http\Service;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller; 
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aMutasiRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aOrderMutasiRepositoryImpl; 
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;

class aMutasiService extends Controller
{
    use AutoNumberTrait;
    private $aBarangRepository;
    private $asupplierRepository;
    private $aPurchaseRequestRepository;
    private $aStokRepository;
    private $aOrderMutasiRepository;
    private $aMasterUnitRepository;
    private $aMutasiRepository;
    private $ahnaRepository;


    public function __construct(
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $asupplierRepository,
        aPurchaseRequisitionRepositoryImpl $aPurchaseRequestRepository,
        aStokRepositoryImpl $aStokRepository,
        aOrderMutasiRepositoryImpl $aOrderMutasiRepository,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aMutasiRepositoryImpl $aMutasiRepository,
        aHnaRepositoryImpl $ahnaRepository
    ) {
        $this->aBarangRepository = $aBarangRepository;
        $this->asupplierRepository = $asupplierRepository;
        $this->aPurchaseRequestRepository = $aPurchaseRequestRepository;
        $this->aStokRepository = $aStokRepository;
        $this->aOrderMutasiRepository = $aOrderMutasiRepository;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->aMutasiRepository = $aMutasiRepository;
        $this->ahnaRepository = $ahnaRepository;
    }
    public function addMutasi(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitTujuan" => "required",
            "UnitOrder" => "required",
            "Notes" => "required",
            "JenisMutasi" => "required",
            "JenisStok" => "required" 
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek kode 
            if ($this->aMasterUnitRepository->getUnitById($request->UnitOrder)->count() < 1) {
                return $this->sendError('Unit Order Code Not Found !', []);
            }
            if ($this->aMasterUnitRepository->getUnitById($request->UnitTujuan)->count() < 1) {
                return $this->sendError('Unit Tujuan Code Not Found !', []);
            }

            // cek kode PR udah ada belom
            if($request->TransactionOrderCode <> ""){
                if ($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransactionOrderCode)->count() < 1) {
                    return $this->sendError('Order Mutasi Code Not Found !', []);
                }
            } 
            

            $getmax = $this->aMutasiRepository->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->MutasiNumber($request, $TransactionCode);

            $this->aMutasiRepository->addMutasi($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Mutasi Create Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addMutasiWithOrderDetail(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "TransactionOrderCode" => "required",
            "UnitOrder" => "required",
            "UnitTujuan" => "required",
            "JenisMutasi" => "required",
            "JenisStok" => "required",
            "Notes" => "required",
            "TotalQtyOrder" => "required",
            "TotalRow" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction(); 

            // // cek ada gak datanya
            if ($this->aMutasiRepository->getMutasibyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Mutasi Number Not Found !', []);
            }

            // validasi Kode
            foreach ($request->Items as $key) {
                # code...
                // // cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                    return $this->sendError('Product Not Found !', []);
                }
            }
            // Validasi Stok cukup engga
            foreach ($request->Items as $key) {
                # code...
                // cek kode barangnya ada ga
                $cekstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->count();
                
                if ( $cekstok < 1) {
                    return  $this->sendError('Qty Stok Tidak ada diLayanan Tujuan Ini ! ' , []);
                }
            }
            foreach ($request->Items as $key) {
                # code...
                 // cek kode barangnya ada ga
                $cekstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
           
                $getdatadetilmutasi = $this->aMutasiRepository->getMutasiDetailbyIDBarang($request, $key);
                $vGetMutasiDetil =  $getdatadetilmutasi->first();
              
                if($getdatadetilmutasi->count() < 1 ){
                    
                        $stokCurrent = (float)$cekstok->Qty;
                 

                    if ($stokCurrent < $key['Konversi_QtyTotal']) {
                        return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $key['QtyMutasi'] . ' ! ', []);
                    }
                }else{
                     
                        $stokCurrent = (float)$cekstok->Qty;
                 
                     $getStokPlus = $vGetMutasiDetil->Konversi_QtyTotal + $stokCurrent;
                     $stokminus = $getStokPlus - $key['Konversi_QtyTotal'];
                    if ($stokminus < 0) {
                        return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $key['QtyMutasi'] . ' ! ', []);
                    } 
                }
                
            }

            foreach ($request->Items as $key) {
                $getdatadetilmutasi = $this->aMutasiRepository->getMutasiDetailbyIDBarang($request,$key);
               // get Hpp Average 
               $getHppBarang = $this->ahnaRepository->getHppAverage($key)->first()->first();
               $xhpp = $getHppBarang->NominalHpp;
               // get Hpp Average

                if($getdatadetilmutasi->count() < 1){
                    if ($key['Konversi_QtyTotal'] > 0) {
                        $this->aMutasiRepository->addMutasiDetail($request, $key,$xhpp); 
                    }
                }else{  
                   $showData = $getdatadetilmutasi->first();
                    $mtSatuan = $showData->Satuan;
                    $mtSatuan_Konversi = $showData->Satuan_Konversi;
                    $mtKonversiQty = $showData->KonversiQty;
                    $mtKonversi_QtyTotal = $showData->Konversi_QtyTotal;
                    $mtQtyMutasi = $showData->QtyMutasi;
                    $mtQtyOrder = $showData->QtyOrder;
                    $mtQtySisa = $showData->QtySisa; 
 
                    if($mtKonversi_QtyTotal <> $key['Konversi_QtyTotal']){ // Dirubah jika Qty nya ada Perubahan Aja
                       $goQtyMutasiSisaheaderBefore = $mtQtyMutasi + $key['QtyMutasi'];
                       $goQtyMutasiSisaheaderAfter = $goQtyMutasiSisaheaderBefore - $key['QtyMutasi'];

                       $goQtyMutasiSisaKovenrsiBefore = $mtKonversi_QtyTotal + $key['Konversi_QtyTotal'];
                       $goQtyMutasiSisaKovenrsiAfter = $goQtyMutasiSisaKovenrsiBefore - $key['Konversi_QtyTotal'];

                        $this->aMutasiRepository->editMutasiDetailbyIdBarang($request,$key,$xhpp);
                        $this->aOrderMutasiRepository->updateQtyOrderMutasi2($request,$key, $goQtyMutasiSisaheaderAfter);

                        if ($request->JenisStok == "STOK") {
                            // replace stok ke awal
                            $getCurrentStok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                            $totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                            $this->aStokRepository->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                            $this->aStokRepository->updateStokTrs($request,$key,$totalstok,$request->UnitOrder);
                            $this->aStokRepository->deleteBukuStok($request, $key, "MT", $request->UnitTujuan);
                            $this->aStokRepository->deleteBukuStok($request, $key, "MT", $request->UnitOrder);
                        }else{
                            // replace stok ke awal
                            $getCurrentStok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                            $totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                            $this->aStokRepository->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                            $this->aStokRepository->deleteBukuStok($request,$key,"MT",$request->UnitTujuan);
                        }
                    } 
                    $this->aOrderMutasiRepository->updateQtyOrderMutasi2($request,$key, 2-1);
                }
                // delete tabel buku
                 
                        // CARI DO TERATAS YANG MASIH ADA QTY
                        first:
                        $getStokFirst = $this->aStokRepository->getStokExpiredFirst($request, $key);

                        //return $getStokFirst;
                        $DeliveryCodex = $getStokFirst->DeliveryCode;
                        
                        $qtyBuku = $getStokFirst->x;
                        $ExpiredDate = $getStokFirst->ExpiredDate;
                        $BatchNumber = $getStokFirst->BatchNumber;

                        if ($qtyBuku < $key['Konversi_QtyTotal']) {
                            $qtynew = $qtyBuku;
                            $persediaan = $qtynew * $xhpp;
                        } else {
                            $qtynew = $key['Konversi_QtyTotal'];
                            $persediaan = $qtynew * $xhpp;
                        }
                        $TipeTrs = "MT";
                        // // INSERT BUKU IN DI LAYANAN GUDANG
                        $this->aStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);

                        // update stok Tujuan / Gudang 
                        if ($this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->count() < 1) {
                            //kalo g ada insert
                            $this->aStokRepository->addStokTrs($request, $key, $qtynew, $request->UnitTujuan);
                        } else {
                            //kallo ada ya update
                            $sumstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan);
                            foreach ($sumstok as $value) {
                                $QtyCurrent = $value->Qty;
                            }
                            $QtyTotal = $QtyCurrent - $qtynew;
                            $this->aStokRepository->updateStokTrs($request, $key, $QtyTotal, $request->UnitTujuan);
                        }

                        //insert stok Lokasi order 
                        if ($request->JenisStok == "STOK") {
                            // INSERT BUKU IN DI LAYANAN TUJUAN
                            $this->aStokRepository->addBukuStokIn($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);

                            if ($this->aStokRepository->cekStokbyIDBarang($key, $request->UnitOrder)->count() < 1) {
                                // kalo g ada insert
                                $this->aStokRepository->addStokTrs($request, $key, $qtynew, $request->UnitOrder);
                            } else {
                                // kallo ada ya update
                                $sumstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitOrder);
                                foreach ($sumstok as $value) {
                                    $QtyCurrent = $value->Qty;
                                }
                                $QtyTotal = $QtyCurrent + $qtynew;
                                $this->aStokRepository->updateStokTrs($request, $key, $QtyTotal, $request->UnitOrder);
                            }
                        } else {
                            $this->aStokRepository->addBukuStokIn($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);
                            $this->aStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);
                        }

                        if ($qtynew < $key['Konversi_QtyTotal']) {
                            $key['Konversi_QtyTotal'] = $key['Konversi_QtyTotal'] - $qtynew;
                            goto first;
                        }
            }
            DB::commit();
            return $this->sendResponse([], 'Items Add Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function editOrderMutasi(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "TransactionOrderCode" => "required",
            "UnitOrder" => "required",
            "UnitTujuan" => "required",
            "JenisMutasi" => "required",
            "TotalQtyOrder" => "required",
            "TotalRow" => "required",
            "JenisStok" => "required",
            "Notes" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // // cek ada gak datanya
            if ($this->aMutasiRepository->getMutasibyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Mutasi Number Not Found !', []);
            }

            // cek order mutasi
            if($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransactionOrderCode)->count() < 1 ){
                return $this->sendError('Order Mutasi Number Not Found !', []);
            }
            
            // edit mutasi
            $this->aMutasiRepository->editMutasi($request);

            // jurnal header



        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function voidOrderMutasi(Request $request)
    {
         
    }
    public function voidPurchaseOrderDetailbyItem(Request $request)
    {
         
    }
    public function getMutasibyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aMutasiRepository->getMutasibyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aMutasiRepository->getMutasibyID($request->TransasctionCode);

            DB::commit();
            return $this->sendResponse($data, 'Mutasi Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Mutasi Data Not Found !', $e->getMessage());
        }
    }
    public function getMutasiDetailbyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aMutasiRepository->getMutasiDetailbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aMutasiRepository->getMutasiDetailbyID($request->TransasctionCode);

            DB::commit();
            return $this->sendResponse($data, 'Mutasi Detail Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Mutasi Detail Data Not Found !', $e->getMessage());
        }
    }
    public function getMutasibyDateUser($request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aMutasiRepository->getMutasibyDateUser($request);

            DB::commit();
            return $this->sendResponse($data, 'Mutasi Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Mutasi detail Data Not Found !', $e->getMessage());
        }
    }
    public function getMutasibyPeriode($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aMutasiRepository->getMutasibyPeriode($request);

            DB::commit();
            return $this->sendResponse($data, 'Mutasi Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Mutasi Data Not Found !', $e->getMessage());
        }
    }
}
