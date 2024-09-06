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
use App\Http\Repository\aJurnalRepositoryImpl;
use App\Traits\FifoTrait;

class aMutasiService extends Controller
{
    use AutoNumberTrait;
    use FifoTrait;
    private $aBarangRepository;
    private $asupplierRepository;
    private $aPurchaseRequestRepository;
    private $aStokRepository;
    private $aOrderMutasiRepository;
    private $aMasterUnitRepository;
    private $aMutasiRepository;
    private $ahnaRepository;
    private $aJurnal;

    public function __construct(
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $asupplierRepository,
        aPurchaseRequisitionRepositoryImpl $aPurchaseRequestRepository,
        aStokRepositoryImpl $aStokRepository,
        aOrderMutasiRepositoryImpl $aOrderMutasiRepository,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aMutasiRepositoryImpl $aMutasiRepository,
        aHnaRepositoryImpl $ahnaRepository,
        aJurnalRepositoryImpl $aJurnal
    ) {
        $this->aBarangRepository = $aBarangRepository;
        $this->asupplierRepository = $asupplierRepository;
        $this->aPurchaseRequestRepository = $aPurchaseRequestRepository;
        $this->aStokRepository = $aStokRepository;
        $this->aOrderMutasiRepository = $aOrderMutasiRepository;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->aMutasiRepository = $aMutasiRepository;
        $this->ahnaRepository = $ahnaRepository;
        $this->aJurnal = $aJurnal;
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

        //dd(date("dmY", strtotime($request->TransactionDate)));
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek kode 
            if ($this->aMasterUnitRepository->getUnitById($request->UnitOrder)->count() < 1) {
                return $this->sendError('Unit Order Kosong !', []);
            }
            if ($this->aMasterUnitRepository->getUnitById($request->UnitTujuan)->count() < 1) {
                return $this->sendError('Unit Tujuan Kosong !', []);
            }

            // cek kode PR udah ada belom
            if($request->TransactionOrderCode <> ""){
                if ($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransactionOrderCode)->count() < 1) {
                    return $this->sendError('Order Mutasi tidak ditemukan !', []);
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
            return $this->sendResponse($autonumber, 'Mutasi berhasil dibuat !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Gagal di Proses !', $e->getMessage());
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
  

            // // cek ada gak datanya
            if ($this->aMutasiRepository->getMutasibyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Mutasi tidak ditemukan!', []);
            }

            // validasi Kode
            foreach ($request->Items as $key) {
                # code...
                // // cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                    return $this->sendError('Kode Barang '.$key['ProductCode'].', tidak ditemukan !', []);
                }
            }
            // Validasi Stok cukup engga
            foreach ($request->Items as $key) {
                # code...
                // cek kode barangnya ada ga
                $cekstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->count();
          
                if ( $cekstok < 1) {
                    return  $this->sendError('Qty Stok ' .$key['NamaBarang']. ' Tidak ada diLayanan Tujuan '. $request->UnitTujuan . ' Ini !', []);
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
                        return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $key['Konversi_QtyTotal'] . ' ! ', []);
                    }
                }else{
                    $stokCurrent = (float)$cekstok->Qty;
                    $getStokPlus = $vGetMutasiDetil->Konversi_QtyTotal + $stokCurrent;
                    $stokminus = $getStokPlus - $key['Konversi_QtyTotal'];
                    if ($stokminus < 0) {
                        return $this->sendError('Qty Stok ' . $key['ProductName'] . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $key['Konversi_QtyTotal'] . ' ! ', []);
                    } 
                }
                
            }
        try {
            // Db Transaction
            DB::beginTransaction(); 

            $this->aJurnal->delJurnalHdr($request);
            $this->aJurnal->delJurnalDtl($request);

            foreach ($request->Items as $key) {
                $getdatadetilmutasi = $this->aMutasiRepository->getMutasiDetailbyIDBarang($request,$key);
                $dataorderMutasidetail = $this->aOrderMutasiRepository->getOrderMutasiDetailbyIDBarangMutasi($request->TransactionOrderCode,$key['ProductCode'])->first();
                    // get Hpp Average 
                    $getHppBarang = $this->ahnaRepository->getHppAverage($key)->first()->first();
                    $xhpp = $getHppBarang->NominalHpp;
                    // get Hpp Average  
                    
                if($getdatadetilmutasi->count() < 1){
                    if ($key['Konversi_QtyTotal'] > 0) {
                        $showData = $getdatadetilmutasi->first();
                        $this->aMutasiRepository->addMutasiDetail($request, $key,$xhpp);  
                        $QtyRemain = $dataorderMutasidetail->QtyOrderMutasi; 
                        $doqtyAfter = $QtyRemain - $key['QtyMutasi'];
                        $this->aOrderMutasiRepository->updateQtyOrderMutasi2($request,$key, $doqtyAfter);
                        $this->fifoMutasi($request,$key);
                    }

                }else{
                    
                    $showData = $getdatadetilmutasi->first();
                    $mtKonversi_QtyTotal = $showData->Konversi_QtyTotal; 
                    if($mtKonversi_QtyTotal <> $key['Konversi_QtyTotal']){ // Dirubah jika Qty nya ada Perubahan Aja 

                        $QtyRemain = $dataorderMutasidetail->QtyOrderMutasi; 
                        $doqtyAfter = $QtyRemain - $key['QtyMutasi'];
                        $this->aOrderMutasiRepository->updateQtyOrderMutasi2($request,$key, $doqtyAfter);

                        if ($request->JenisStok == "STOK") {
                            // replace stok ke awal
                            // $getCurrentStok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                            // $getCurrentStokOrder = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitOrder)->first();
                            // $totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                            // $totalstokOrder = $getCurrentStokOrder->Qty - $mtKonversi_QtyTotal;
                            // dd( $totalstokOrder);
                        //    $this->aStokRepository->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                        //    $this->aStokRepository->updateStokTrs($request,$key,$totalstokOrder,$request->UnitOrder);
                            $this->aStokRepository->deleteBukuStok($request, $key, "MT", $request->UnitTujuan);
                            $this->aStokRepository->deleteDataStoks($request, $key, "MT", $request->UnitTujuan);
                            $this->aStokRepository->deleteBukuStok($request, $key, "MT", $request->UnitOrder);
                            $this->aStokRepository->deleteDataStoks($request, $key, "MT", $request->UnitOrder);
                        }else{
                            // replace stok ke awal
                            // $getCurrentStok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->first();
                            // $totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal;
                            // $this->aStokRepository->updateStokTrs($request,$key,$totalstok,$request->UnitTujuan);
                            $this->aStokRepository->deleteBukuStok($request,$key,"MT",$request->UnitTujuan);
                        }
                        $this->fifoMutasi($request,$key);
                    }  
                }
               $this->aMutasiRepository->editMutasiDetailbyIdBarang($request,$key,$xhpp);
 

         
                // jurnal
                $note = 'Persediaan Mutasi Barang '. $key['ProductName'].' No. Pemakaian : ' . $request->TransactionCode . ' Konversi_QtyTotal : ' . $key['Konversi_QtyTotal'];
                $noteHpp = 'Hpp Persediaan Mutasi Barang '. $key['ProductName'].' No. Pemakaian : ' . $request->TransactionCode . ' Konversi_QtyTotal : ' . $key['Konversi_QtyTotal'];
                $cekrek = $this->aJurnal->getRekeningPersediaaan($key['ProductCode']);  

                if ($request->JenisStok == "STOK") {

                    $this->aJurnal->addJurnalDetailDebetPersediaanGlobal(
                        $note, $cekrek->first()->RekPersediaan,
                        $request->TransactionCode,
                        $key['Konversi_QtyTotal'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitOrder
                    );  

                    $this->aJurnal->addJurnalDetailKreditPersediaanGlobal(
                        $note, $cekrek->first()->RekPersediaan,
                        $request->TransactionCode,
                        $key['Konversi_QtyTotal'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitTujuan
                    );  

                }else{
                    $this->aJurnal->addJurnalDetailDebetPersediaanGlobal(
                        $note, $cekrek->first()->RekPersediaan,
                        $request->TransactionCode,
                        $key['Konversi_QtyTotal'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitOrder
                    );  
                    // $this->aJurnal->addJurnalDetailKreditPersediaanGlobal(
                    //     $note, $cekrek->first()->RekHpp,
                    //     $request->TransactionCode,
                    //     $key['Konversi_QtyTotal'],$xhpp,
                    //     $key['ProductCode'],$key['ProductName'],$request->UnitOrder
                    // );  

                    $this->aJurnal->addJurnalDetailKreditPersediaanGlobal(
                        $note, $cekrek->first()->RekPersediaan,
                        $request->TransactionCode,
                        $key['Konversi_QtyTotal'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitTujuan
                    );   
                    // $this->aJurnal->addJurnalDetailDebetHppGlobal(
                    //     $note, $cekrek->first()->RekHpp,
                    //     $request->TransactionCode,
                    //     $key['Konversi_QtyTotal'],$xhpp,
                    //     $key['ProductCode'],$key['ProductName'],$request->UnitTujuan
                    // ); 

                    $this->aJurnal->addJurnalDetailKreditPersediaanGlobal(
                        $note, $cekrek->first()->RekPersediaan,
                        $request->TransactionCode,
                        $key['Konversi_QtyTotal'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitOrder
                    );   
                    $this->aJurnal->addJurnalDetailDebetHppGlobal(
                        $noteHpp, $cekrek->first()->RekHpp,
                        $request->TransactionCode,
                        $key['Konversi_QtyTotal'],$xhpp,
                        $key['ProductCode'],$key['ProductName'],$request->UnitOrder
                    ); 
                }
                // jurnal
            }

              // edit mutasi
              $notex = 'Mutasi Barang , No. Mutasi : ' . $request->TransactionCode;
              $this->aJurnal->addJurnalHeaderMutasi($request, $notex);
              $this->aMutasiRepository->editMutasi($request);

            DB::commit();
            return $this->sendResponse([], 'Items Add Successfully !');
 
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
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
    public function voidMutasi(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required",  
            "UnitOrder" => "required",
            "UnitTujuan" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

         // // cek ada gak datanya
         if ($this->aMutasiRepository->getMutasibyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Transaksi Mutasi tidak ditemukan !', []);
        }

        // cek kode unit order
        if ($this->aMasterUnitRepository->getUnitById($request->UnitOrder)->count() < 1) {
            return $this->sendError('Unit Order Mutasi tidak ditemukan !', []);
        }
        // cek kode unit tujuan
        if ($this->aMasterUnitRepository->getUnitById($request->UnitTujuan)->count() < 1) {
            return $this->sendError('Unit Tujuan Mutasi tidak ditemukan !', []);
        }
        // cek kode unit ini bener ga atas transaksi ini
        $cekdodetil = $this->aMutasiRepository->getMutasibyIDUnitOrder($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
        }

        // VALIDASI BY BARANG
        $dtlconsumable = $this->aMutasiRepository->getMutasiDetailbyID($request->TransactionCode);
        
        foreach ($dtlconsumable as $key2) {
            // BARANG - cek kode barangnya ada ga
            if ($this->aBarangRepository->getBarangbyId($key2->ProductCode)->count() < 1) {
                return $this->sendError('Kode Barang tidak ditemukan !', []);
            } 
 
            // BARANG - cek Stok
            $cekqtystok = $this->aStokRepository->cekStokbyIDBarangOnlyMutasi($key2->ProductCode,$request->UnitTujuan)->first();
            $stokCurrent = (float)$cekqtystok->Qty;
           
                if ($stokCurrent < $key2->Konversi_QtyTotal) {
                    return $this->sendError('Qty Stok ' . $key2->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $key2->Konversi_QtyTotal . ' ! ', []);
                }

        }
        try {
            // Db Transaction
            DB::beginTransaction(); 

            //eksekusi
            $dtlconsumable = $this->aMutasiRepository->getMutasiDetailbyID($request->TransactionCode);
            foreach ($dtlconsumable as $key2) {
                $Konversi_QtyTotal = $key2->Konversi_QtyTotal;
                // UPDATE STOK PLUS KE LOKASI STOK TUJUAN
                // $cekqtystok = $this->aStokRepository->cekStokbyIDBarangOnlyMutasi($key2->ProductCode,$request->UnitTujuan);
                // foreach ($cekqtystok as $valueStok) {
                //     $datastok = $valueStok->Qty;
                // } 
                // $sisaStok = $datastok + $Konversi_QtyTotal;   
                // $this->aStokRepository->updateStokPerItemMutasi($key2->ProductCode, $sisaStok, $request->UnitTujuan);
                $cekBuku = $this->aStokRepository->cekBukuByTransactionandCodeProduct($key2->ProductCode,$key2,'MT');
                foreach ($cekBuku as $data) {
                    $asd = $data;
                } 
                $this->aStokRepository->addBukuStokInVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal, $request->UnitOrder);
                $this->aStokRepository->addDataStoksInVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal, $request->UnitOrder);
                // UPDATE STOK PLUS KE LOKASI STOK TUJUAN

                if($request->JenisStok == "STOK"){ 
                    // UPDATE STOK MINUS KE LOKASI STOK ORDER
                        // $cekqtystok = $this->aStokRepository->cekStokbyIDBarangOnlyMutasi($key2->ProductCode,$request->UnitOrder);
                        // foreach ($cekqtystok as $valueStok) {
                        //     $datastok = $valueStok->Qty;
                        // } 
                        // $sisaStok = $datastok - $Konversi_QtyTotal;   
                        // $this->aStokRepository->updateStokPerItemMutasi($key2->ProductCode, $sisaStok, $request->UnitOrder);
                        $cekBuku = $this->aStokRepository->cekBukuByTransactionandCodeProduct($key2->ProductCode,$key2,'MT');
                        foreach ($cekBuku as $data) {
                            $asd = $data;
                        } 
                        $this->aStokRepository->addBukuStokOutVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal,$request->UnitTujuan);
                        $this->aStokRepository->addDataStoksOutVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal,$request->UnitTujuan); 
                    // UPDATE STOK MINUS KE LOKASI STOK ORDER
                } 
                    $dataorderMutasidetail = $this->aOrderMutasiRepository->getOrderMutasiDetailbyIDBarangMutasi($request->TransactionOrderCode,$key2->ProductCode)->first();
                    $this->aOrderMutasiRepository->updateQtyOrderMutasi3($request,$key2->ProductCode, $dataorderMutasidetail->QtySisaMutasi+$key2->QtyMutasi);
                    $this->aMutasiRepository->voidMutasiDetailbyItemAll($request,$key2->ProductCode);

            
            }
                    $this->aMutasiRepository->voidMutasi($request);
                    DB::commit();
                    return $this->sendResponse([], 'Transaksi Mutasi berhasil di Hapus !');
        }catch (Exception $e) {
                    DB::rollBack();
                    Log::info($e->getMessage());
                    return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }
    }
    public function voidMutasiDetailbyItem(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required", 
            "ProductCode" => "required",
            "UnitOrder" => "required",
            "UnitTujuan" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);


        // // cek ada gak datanya
        if ($this->aMutasiRepository->getMutasibyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Transaksi Mutasi tidak ditemukan !', []);
        }
        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitOrder)->count() < 1) {
            return $this->sendError('Unit Prder Mutasi tidak ditemukan !', []);
        }

        if ($this->aMasterUnitRepository->getUnitById($request->UnitTujuan)->count() < 1) {
            return $this->sendError('Unit Tujuan Mutasi tidak ditemukan !', []);
        }
        // cek kode unit ini bener ga atas transaksi ini
        $cekdodetil = $this->aMutasiRepository->getMutasibyIDUnitOrder($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
        }

        // BARANG - cek kode barangnya ada ga
        if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
            return $this->sendError('Kode Barang tidak ditemukan !', []);
        } 

        // BARANG - cek aktif engga
        $cekdodetil = $this->aMutasiRepository->getMutasiDetailbyIDandProductCode($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Barang Sudah di Batalkan !', []);
        }

        // BARANG - cek Stok
        $cekqtystok = $this->aStokRepository->cekStokbyIDBarangOnlyMutasi($request->ProductCode,$request->UnitTujuan)->first();
        $stokCurrent = (float)$cekqtystok->Qty;
            if ($stokCurrent < $request->Konversi_QtyTotal) {
                return $this->sendError('Qty Stok ' . $request->ProductName . ' Tidak Cukup, Qty Stok ' . $stokCurrent . ', Qty Mutasi ' . $request->Konversi_QtyTotal . ' ! ', []);
            }
            
        // BARANG - cek aktif engga getMutasiDetailbyID
        $cekdodetil = $this->aMutasiRepository->getMutasiDetailbyID($request->TransactionCode)->count();
        if ($cekdodetil <= 1) {
              return $this->sendError('Kode Barang atas Transaksi Mutasi ini Hanya 1, Silahkan Hapus semua Transaksi Mutasi Beli ini !', []);
        }
        
        try {
            // Db Transaction
            DB::beginTransaction(); 

            $dtlMutasi = $this->aMutasiRepository->getMutasiDetailbyIDandProductCode($request)->first();
            $Konversi_QtyTotal = $dtlMutasi->Konversi_QtyTotal;

            // UPDATE STOK PLUS KE LOKASI STOK TUJUAN
                // $cekqtystok = $this->aStokRepository->cekStokbyIDBarangOnlyMutasi($request->ProductCode,$request->UnitTujuan);
                // foreach ($cekqtystok as $valueStok) {
                //     $datastok = $valueStok->Qty;
                // } 
                // $sisaStok = $datastok + $Konversi_QtyTotal;   
                // $this->aStokRepository->updateStokPerItemMutasi($request->ProductCode, $sisaStok, $request->UnitTujuan);
                $cekBuku = $this->aStokRepository->cekBukuByTransactionandCodeProduct($request->ProductCode,$request,'MT');
                foreach ($cekBuku as $data) {
                    $asd = $data;
                    
                } 
                $this->aStokRepository->addBukuStokInVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal,$request->UnitOrder);
                $this->aStokRepository->addDataStoksInVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal,$request->UnitOrder); 
            // UPDATE STOK PLUS KE LOKASI STOK TUJUAN

            if($request->JenisStok == "STOK"){ 
                // UPDATE STOK MINUS KE LOKASI STOK ORDER
                // $cekqtystok = $this->aStokRepository->cekStokbyIDBarangOnlyMutasi($request->ProductCode,$request->UnitOrder);
                // foreach ($cekqtystok as $valueStok) {
                //     $datastok = $valueStok->Qty;
                // } 
                // $sisaStok = $datastok - $Konversi_QtyTotal;   
                // $this->aStokRepository->updateStokPerItemMutasi($request->ProductCode, $sisaStok, $request->UnitOrder);
                $cekBuku = $this->aStokRepository->cekBukuByTransactionandCodeProduct($request->ProductCode,$request,'MT');
                foreach ($cekBuku as $data) {
                    $asd = $data;
                }  
                $this->aStokRepository->addBukuStokOutVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal,$request->UnitTujuan);
                $this->aStokRepository->addDataStoksOutVoidFromSelectMutasi($asd,'MT_V',$request,$Konversi_QtyTotal,$request->UnitTujuan);
                // UPDATE STOK MINUS KE LOKASI STOK ORDER
            }
            
            $dataorderMutasidetail = $this->aOrderMutasiRepository->getOrderMutasiDetailbyIDBarangMutasi($request->TransactionOrderCode,$request->ProductCode)->first();
            $this->aOrderMutasiRepository->updateQtyOrderMutasi3($request,$request->ProductCode, $dataorderMutasidetail->QtySisaMutasi+$dtlMutasi->QtyMutasi);
            $this->aMutasiRepository->voidMutasiDetailbyItem($request);

            DB::commit();
            return $this->sendResponse([], 'Mutasi Detil Berhasil dihapus !');

        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }

    }
    public function getMutasibyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required"
        ]);
        try {
            // cek ada gak datanya
            if ($this->aMutasiRepository->getMutasibyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Mutasi tidak ditemukan !', []);
            }
            $data = $this->aMutasiRepository->getMutasibyID($request->TransasctionCode);
            return $this->sendResponse($data, 'No. Transaksi Mutasi ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Transaksi Mutasi tidak ditemukan !', $e->getMessage());
        }
    }
    public function getMutasiDetailbyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required"
        ]);

        try {
            // cek ada gak datanya
            if ($this->aMutasiRepository->getMutasiDetailbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }
            $data = $this->aMutasiRepository->getMutasiDetailbyID($request->TransasctionCode);
            return $this->sendResponse($data, 'No. Transaksi Mutasi ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Transaksi Mutasi tidak ditemukan !', $e->getMessage());
        }
    }
    public function getMutasibyDateUser($request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);
        try {
            $data = $this->aMutasiRepository->getMutasibyDateUser($request);
            return $this->sendResponse($data, 'No. Transaksi Mutasi ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Transaksi Mutasi tidak ditemukan !', $e->getMessage());
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
            $data = $this->aMutasiRepository->getMutasibyPeriode($request);
            return $this->sendResponse($data, 'No. Transaksi Mutasi ditemukan  !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('No. Transaksi Mutasi tidak ditemukan !', $e->getMessage());
        }
    }
}
