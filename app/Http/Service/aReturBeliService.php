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
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;
use App\Http\Repository\aReturBeliRepositoryImpl;
use App\Traits\FifoTrait;

class aReturBeliService extends Controller
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
    private $returbeliRepository;

    public function __construct(
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $aSupplierRepository, 
        aStokRepositoryImpl $aStok,
        aDeliveryOrderRepositoryImpl $aDeliveryOrder,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aHnaRepositoryImpl $ahnaRepository ,
        aReturBeliRepositoryImpl $returbeliRepository
    ) {
        $this->aBarangRepository = $aBarangRepository;
        $this->aSupplierRepository = $aSupplierRepository;
        $this->aStok = $aStok;
        $this->aDeliveryOrder = $aDeliveryOrder;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->ahnaRepository = $ahnaRepository; 
        $this->returbeliRepository = $returbeliRepository; 
    }

    public function addReturBeliHeader(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitCode" => "required",
            "DeliveryCode" => "required",
            "Notes" => "required",
            "SupplierCode" => "required" 
        ]);
        try {

            // Db Transaction
            DB::beginTransaction();

            // cek deliveryCode 
            if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->DeliveryCode)->count() < 1) {
                return $this->sendError('No. Delivery Order tidak ditemukan !', []);
            }


            $getmax = $this->returbeliRepository->getMaxCode($request);
           
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
        

            $autonumber = $this->ReturBeliNumber($request, $TransactionCode);
            $this->returbeliRepository->addReturBeliHeader($request, $autonumber);
            $datadetail = $this->returbeliRepository->gettempReturDoStok($request);

            $response = [
                'notrs' => $autonumber, 
                'items' => $datadetail , 
            ];
            DB::commit();
            return $this->sendResponse($response, 'Transaksi Retur Beli Berhasil dibuat !');

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }
    }
    public function addReturBeliFinish(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required",
            "DeliveryCode" => "required",
            "UnitCode" => "required", 
            "SupplierCode" => "required",
            "Notes" => "required",
            "TotalQtyReturBeli" => "required",
            "TotalRow" => "required",
            "TotalQtyReturBeli" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);

            // cek ada gak datanya
            if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->DeliveryCode)->count() < 1) {
                return $this->sendError('No. Transaksi Delivery Order tidak ditemukan !', []);
            }

            // validasi Kode barang dengan DO
            foreach ($request->Items as $key) {
                if ($this->aDeliveryOrder->getDeliveryOrderDetailbyIDandProductCodeLoopRetur($request->DeliveryCode,$key['ProductCode'])->count() < 1) {
                    return $this->sendError('Kode Barang : '. $key['ProductCode'].' - '. $key['ProductName'].' pada Delivery Order ini tidak ada  !', []);
                }
            }

            // validasi stok
            foreach ($request->Items as $key) {
                $Konversi_QtyTotal = $key['QtyRetur']*$key['KonversiQty'];
                $cekqtystok = $this->aStok->cekStokbyIDBarangOnly($key['ProductCode'], $request);
                
                foreach ($cekqtystok as $valueStok) {
                    $datastok = $valueStok->Qty;
                }
                $sisaStok = $datastok - $Konversi_QtyTotal;
                if($sisaStok < 0 ){
                    return $this->sendError('Barang ' . $key['ProductName'] . ' Stok Saat ini : '. $datastok/$key['KonversiQty'] .', Nilai Retur Beli Qty : ' . $key['QtyRetur'] . ', Void Delivery Order Dibatalkan !', []);
                }
            }

             // validasi stok
             foreach ($request->Items as $key) {
                $getDetailretur = $this->returbeliRepository->getReturBeliDetailbyIDBarang($request,$key); 
                $dataDeliveryOrdrDetail = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDandProductCodeLoopRetur($request->DeliveryCode,$key['ProductCode'])->first();
               
                if($dataDeliveryOrdrDetail->QtyDeliveryRemain < $key['QtyRetur'] ){
                    return $this->sendError('Barang ' . $key['ProductName'] . ' Qty Sisa Deliver Saat ini : '. $dataDeliveryOrdrDetail->QtyDeliveryRemain .', Nilai Retur Beli Qty : ' . $key['QtyRetur'] . ', Retur Dibatalkan !', []);
                }
             }

        try {
            // Db Transaction
            DB::beginTransaction(); 
            
            foreach ($request->Items as $key) {
                $getDetailretur = $this->returbeliRepository->getReturBeliDetailbyIDBarang($request,$key); 
                $dataDeliveryOrdrDetail = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDandProductCodeLoopRetur($request->DeliveryCode,$key['ProductCode'])->first();
                if($getDetailretur->count() < 1){
                    if ($key['QtyRetur'] > 0) {
                        $this->returbeliRepository->addReturBeliDetail($key,$request->TransactionCode);
                        $doQtyRemain = $dataDeliveryOrdrDetail->QtyDeliveryRemain; 
                        $doqtyAfter = $doQtyRemain - $key['QtyRetur'];
                        $this->aDeliveryOrder->updateQtyRemainDeliveryOrderRetur($request,$key,$doqtyAfter); 
                        $this->fifoOut($request,$key,'RB');
                    }
                }else{  
                    $showData = $getDetailretur->first(); 
                   if($showData->QtyRetur <> $key['QtyRetur']){ // Dirubah jika Qty nya ada Perubahan Aja
                        $mtKonversi_QtyTotal = $key['KonversiQty']*$showData->QtyRetur; 
                          // replace stok ke awal
                          $getCurrentStok = $this->aStok->cekStokbyIDBarang($key, $request->UnitCode)->first();
                          $totalstok = $getCurrentStok->Qty + $mtKonversi_QtyTotal; 
                          $this->aStok->updateStokTrs($request,$key,$totalstok,$request->UnitCode);
                          $this->fifoOut($request,$key,'RB');
                   } 
                   $doQtyRemain = $dataDeliveryOrdrDetail->QtyDeliveryRemain;
                   $doQtyBefore = $doQtyRemain + $showData->QtyRetur;
                   $doqtyAfter = $doQtyBefore - $key['QtyRetur'];
                   $this->aDeliveryOrder->updateQtyRemainDeliveryOrderRetur($request,$key,$doqtyAfter);
                } 
                $this->returbeliRepository->editReturBeliDetaibyIdBarang($request,$key);    
            }

            DB::commit();
            return $this->sendResponse([], 'Transaksi Retur Beli Detail Berhasil di Edit !');

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }
    }

    public function voidReturBeliDetailbyItem(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required", 
            "DeliveryCode" => "required", 
            "ProductCode" => "required",
            "ProductName" => "required",
            "UnitCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required",
            "ReasonVoid" => "required",
            "KonversiQty" => "required",
            "Konversi_QtyTotal" => "required"
        ]);

        // // cek ada gak datanya
        if ($this->returbeliRepository->getReturBelibyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Retur Beli Tidak ditemukan !', []);
        }

        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Kode Unit Retur Beli Tidak ditemukan !', []);
        } 

        // cek kode unit ini bener ga atas transaksi ini
        $cekdodetil = $this->returbeliRepository->getReturBelibyIDUnitOrder($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
        }

        
        // BARANG - cek kode barangnya ada ga
        if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
            return $this->sendError('Kode Barang tidak ditemukan !', []);
        } 

        // BARANG - cek aktif engga
        $cekdodetil = $this->returbeliRepository->getReturBeliDetailbyIDandProductCode($request)->count();
        if ($cekdodetil < 1) {
              return $this->sendError('Kode Barang atas Retur beli ini Sudah di Batalkan !', []);
        }

        // BARANG - cek aktif engga getMutasiDetailbyID
        $cekdodetil = $this->returbeliRepository->getReturBeliDetailbyIDOnly($request)->count();
        if ($cekdodetil < 1) {
              return $this->sendError('Kode Barang atas Retur beli ini Hanya 1, Silahkan Hapus semua Transaksi Retur Beli ini !', []);
        }
        
        try {
            // Db Transaction
            DB::beginTransaction(); 

            $dtlReturBeli = $this->returbeliRepository->getReturBeliDetailbyIDandProductCode($request)->first();
            $Konversi_QtyTotal = $dtlReturBeli->Konversi_Qty_Total;
 
            $dataDeliveryOrdrDetail = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDandProductCodeLoopRetur($request->DeliveryCode,$request->ProductCode)->first();
            $doQtyRemain = $dataDeliveryOrdrDetail->QtyDeliveryRemain; 
            $doqtyAfter = $doQtyRemain + $dtlReturBeli->QtyRetur;
 
            $this->aDeliveryOrder->updateQtyRemainDeliveryOrderReturVoid($request,$doqtyAfter,$request->ProductCode); 
            $this->returbeliRepository->voidReturBeliDetailbyItem($request,$request->ProductCode);    

            // UPDATE STOK PLUS KE LOKASI STOK TUJUAN
                $cekqtystok = $this->aStok->cekStokbyIDBarangOnlyMutasi($request->ProductCode,$request->UnitCode);
                foreach ($cekqtystok as $valueStok) {
                    $datastok = $valueStok->Qty;
                } 
                $sisaStok = $datastok + $Konversi_QtyTotal;   
                $this->aStok->updateStokPerItemMutasi($request->ProductCode, $sisaStok, $request->UnitCode);
                $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($request->ProductCode,$request,'RB');
                foreach ($cekBuku as $data) {
                    $asd = $data;
                } 
                $this->aStok->addBukuStokOutVoidFromSelectMutasi($asd,'RB_V',$request,$Konversi_QtyTotal,$request->UnitCode);
            // UPDATE STOK PLUS KE LOKASI STOK TUJUAN

            DB::commit();
            return $this->sendResponse([], 'Transaksi Retur Beli Detail Berhasil di Hapus !');

        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }

    }

    public function voidReturBeli(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required",  
            "DeliveryCode" => "required",
            "UnitCode" => "required",
            "ReasonVoid" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

        // // cek ada gak datanya
        if ($this->returbeliRepository->getReturBelibyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('No. Transaksi Retur Beli Tidak ditemukan !', []);
        }

        // cek kode 
        if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
            return $this->sendError('Kode Unit Retur Beli Tidak ditemukan !', []);
        } 

        // cek kode unit ini bener ga atas transaksi ini
        $cekdodetil = $this->returbeliRepository->getReturBelibyIDUnitOrder($request)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Transaksi ini berbeda kode unitnya, cek data anda kembali !', []);
        }
        // cek kode unit ini bener ga atas transaksi ini
        $cekdodetil = $this->returbeliRepository->getReturBelibyID($request->TransactionCode)->count();
        if ($cekdodetil < 1) {
            return $this->sendError('Kode Transaksi Retur Beli Sudah di Batalkan !', []);
        }
        
        try {
            // Db Transaction
            DB::beginTransaction(); 
            $dtlReturBeli = $this->returbeliRepository->getReturBeliDetailbyIDOnly($request);
          
            foreach ($dtlReturBeli as $key2) {
             
                $Konversi_QtyTotal = $key2->Konversi_Qty_Total;
               
                $dataDeliveryOrdrDetail = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDandProductCodeLoopRetur($request->DeliveryCode,$key2->ProductCode)->first();
                $doQtyRemain = $dataDeliveryOrdrDetail->QtyDeliveryRemain; 
                $doqtyAfter = $doQtyRemain + $key2->QtyRetur;

                $this->aDeliveryOrder->updateQtyRemainDeliveryOrderReturVoid($request,$doqtyAfter,$key2->ProductCode); 
                $this->returbeliRepository->voidReturBeliDetailbyItem($request,$key2->ProductCode);    

                 // UPDATE STOK PLUS KE LOKASI STOK TUJUAN
                    $cekqtystok = $this->aStok->cekStokbyIDBarangOnlyMutasi($key2->ProductCode,$request->UnitCode);
            
                    foreach ($cekqtystok as $valueStok) {
                        $datastok = $valueStok->Qty;
                    } 
                    $sisaStok = $datastok + $Konversi_QtyTotal;   
                    $this->aStok->updateStokPerItemMutasi($key2->ProductCode, $sisaStok, $request->UnitCode);
                    $cekBuku = $this->aStok->cekBukuByTransactionandCodeProduct($key2->ProductCode,$request,'RB');
                    foreach ($cekBuku as $data) {
                        $asd = $data;
                    } 
                    $this->aStok->addBukuStokOutVoidFromSelectMutasi($asd,'RB_V',$request,$Konversi_QtyTotal,$request->UnitCode);
                // UPDATE STOK PLUS KE LOKASI STOK TUJUAN

            }
            $this->returbeliRepository->voidReturBeli($request);
            DB::commit();
            return $this->sendResponse([], 'Transaksi Retur Beli Detail Berhasil di Hapus !');

        }catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }

    }

    public function getReturBelibyID(Request $request)
    {
          // validate 
          $request->validate([
            "TransasctionCode" => "required"
        ]);
        try {
            // cek ada gak datanya
            $data = $this->returbeliRepository->getReturBelibyID($request->TransasctionCode);
            if ($data->count() < 1) {
                return $this->sendError('Transaksi Retur Beli tidak di temukan !', []);
            }
           
            return $this->sendResponse($data, 'Transaksi Retur Beli ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Retur Beli tidak ditemukans !', $e->getMessage());
        }

    }

    public function getReturBeliDetailbyID(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);

        try {
            // cek ada gak datanya
            $data = $this->returbeliRepository->getReturBeliDetailbyIDOnly($request);
            if ($data->count() < 1) {
                return $this->sendError('Transaksi Retur Beli tidak ditemukan !', []);
            }
            
            return $this->sendResponse($data, 'Transaksi Retur Beli ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Retur Beli tidak ditemukans !', $e->getMessage());
        }
    }
    public function getReturBelibyDateUser(Request $request)
    {
         // validate 
         $request->validate([
            "UserCreate" => "required"
        ]);
        try {
            $data = $this->returbeliRepository->getReturBelibyDateUser($request);
            return $this->sendResponse($data, 'Transaksi Retur Beli ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Retur Beli tidak ditemukan !', $e->getMessage());
        }
    }
    public function getReturBelibyPeriode(Request $request)
    {
         // validate 
         $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);
        try {
            $data = $this->returbeliRepository->getReturBelibyPeriode($request);
            return $this->sendResponse($data, 'Transaksi Retur Beli ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Retur Beli tidak ditemukan !', $e->getMessage());
        }
    }

}