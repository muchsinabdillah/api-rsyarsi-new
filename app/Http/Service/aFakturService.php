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
use App\Http\Repository\aFakturRepositoryImpl;
use App\Http\Repository\aJurnalRepositoryImpl; 
use App\Http\Repository\aSupplierRepositoryImpl;

use App\Http\Repository\aDeliveryOrderRepositoryImpl; 
use App\Http\Repository\aPurchaseOrderRepositoryImpl; 

class aFakturService extends Controller
{
    use AutoNumberTrait;
    private $aDeliveryOrder;
    private $aBarangRepository;
    private $aSupplierRepository;
    private $aPurchaseOrderRepository;
    private $aBukuStok;
    private $aStok;
    private $aHna;
    private $aJurnal;
    private $aFakturRepository;

    public function __construct(
        aDeliveryOrderRepositoryImpl $aDeliveryOrder,
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $aSupplierRepository,
        aPurchaseOrderRepositoryImpl $aPurchaseOrderRepository,
        aStokRepositoryImpl $aStok,
        aHnaRepositoryImpl $aHna,
        aJurnalRepositoryImpl $aJurnal,
        aFakturRepositoryImpl $aFakturRepository
    ) {
        $this->aDeliveryOrder = $aDeliveryOrder;
        $this->aBarangRepository = $aBarangRepository;
        $this->aSupplierRepository = $aSupplierRepository;
        $this->aPurchaseOrderRepository = $aPurchaseOrderRepository;
        $this->aStok = $aStok;
        $this->aHna = $aHna;
        $this->aJurnal = $aJurnal;
        $this->aFakturRepository = $aFakturRepository;
    }
    public function addFakturHeader(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "SupplierCode" => "required",
            "Keterangan" => "required",
            "DeliveryCode" => "required",
            "TipeHutang" => "required", 
            "NoFakturPBF" => "required",
            "DateFakturPBF" => "required",
            "NoFakturPajak" => "required", 
            "UnitPembelian" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek supplier kode 
            if ($this->aSupplierRepository->getSupplierbyId($request->SupplierCode)->count() < 1) {
                return $this->sendError('Supplier Code Not Found !', []);
            }
            $getmax = $this->aFakturRepository->getMaxCode($request);
             
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
         
            $autonumber = $this->FakturNumber($request, $TransactionCode);
          
            $this->aFakturRepository->addFaktur($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Create Faktur Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addFakturDetail(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "NoPurchaseOrder" => "required",
            "Pph23" => "required",
            "DeliveryCode" => "required",
            "UnitPembelian" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "SupplierCode" => "required",
            "NoFakturPBF" => "required",
            "DateFakturPBF" => "required",
            "NoFakturPajak" => "required",
            "Keterangan" => "required" ,
            "TotalRow" => "required" ,
            "TotalQty" => "required" ,
            "TotalNilaiFaktur" => "required" ,
            "TglJatuhTempo" => "required" ,
            "TipeHutang" => "required" ,  
            "Subtotal" => "required" ,
            "Grandtotal" => "required"  
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();
            $kodePo = $request->TransactionCode;

            // // cek ada gak datanya
            // if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode)->count() < 1) {
            //     return $this->sendError('Purchase Order Number Not Found !', []);
            // }
             
            // validasi Kode
            foreach ($request->Items as $key) {
                # code...
                // // cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                    return $this->sendError('Product Not Found !', []);
                }
            }
            foreach ($request->Items as $key) {
                # code...
                // // cek kode barangnya ada ga
                if ($this->aDeliveryOrder->getDeliveryOrderDetailByBarang($key['ProductCode'], $request->DeliveryCode)->count() < 1) {
                    return $this->sendError('Product Code Not Found in Delivery Order !', []);
                }
            }

            foreach ($request->Items as $key) {
                // // cek kode barangnya ada ga
                if ($this->aFakturRepository->getFakturDetailByBarang($key['ProductCode'], $request->TransactionCode)->count() < 1) {
                    // Jika Kosong
                    $this->aFakturRepository->addFakturDetil($key, $kodePo, $request);

                    // cari Do dulu
                    $datado = $this->aDeliveryOrder->getDeliveryOrderDetailByBarang($key['ProductCode'], $request->DeliveryCode)->first();
                    $qtyDoRemin = $datado->QtyDeliveryRemain-$key['QtyFaktur'];

                    //update qty remain do
                    $this->aFakturRepository->editQtyDeliveryOrderRemain($request,$qtyDoRemin, $key['ProductCode']);

                } else {
                    // Jika Tidak Kosong
                    // cari Do dulu
                    $datado = $this->aDeliveryOrder->getDeliveryOrderDetailByBarang($key['ProductCode'], $request->DeliveryCode)->first();
                    $datadtlFaktru = $this->aFakturRepository->getFakturDetailByBarang($key['ProductCode'], $request->TransactionCode)->first();
                    $qtyBefore = $datado->QtyDeliveryRemain + $datadtlFaktru->QtyFaktur;
                    $qtyDoAfterRemain = $qtyBefore-$key['QtyFaktur'];
                    //update qty remain do
                    $this->aFakturRepository->editQtyDeliveryOrderRemain($request, $qtyDoAfterRemain, $key['ProductCode']);
                }
            }

            // CARI DULU UTANGNYA ADA ?
            // JURNAL
            $this->aJurnal->delJurnalHdr($request);
            $this->aJurnal->delJurnalDtl($request);
            $rekHutangbarang = $this->aJurnal->getRekHutangBarang()->first();
            $rekPPN = $this->aJurnal->getRekPPNMasukan()->first();
            $rekdiskondetil = $this->aJurnal->getRekDiskonPembelianDetil()->first();
            $rekdiskonpembelianLain = $this->aJurnal->getRekDiskonPembelianlain()->first();
            $rekbiayalain = $this->aJurnal->getRekBiayaPembelianlain()->first();

            // jurnal hdr 
            if ($request->TipeHutang == "1") {
                $rekeningHutang = "31100001";
            } else {
                $rekeningHutang = "31100002";
            }

            $notes = 'Faktur Pembelian No. : ' . $request->TransactionCode . ' , No Penerimaan Barang : ' . $request->DeliveryCode;
            $notehtg = 'Faktur Pembelian No. : ' . $request->TransactionCode . ' , No Penerimaan Barang : ' . $request->DeliveryCode;
            $getHutang = $this->aFakturRepository->getHutangbyID($request);
 
            if ($getHutang->count() < 1) {

                // Generate Hutang
                $getmaxhtg = $this->aFakturRepository->getMaxCodeHutang($request);

                if ($getmaxhtg->count() > 0) {
                    foreach ($getmaxhtg as $datanumber) {
                        $TransactionCodehtg = $datanumber->KD_HUTANG;
                    }
                } else {
                    $TransactionCodehtg = 0;
                }
                $autonumberHtg = $this->HuatngNumber($request, $TransactionCodehtg);
                $notehtg = 'Faktur Pembelian No. : ' . $request->TransactionCode . ' , No Penerimaan Barang : ' . $request->DeliveryCode;
                if ($request->TipeHutang == '1'){
                    $kdrekening = '31100001';
                }else{
                    $kdrekening = '31100002';
                }
                $this->aFakturRepository->addHutangHeader($notehtg, $request, $autonumberHtg,$kdrekening);
                $this->aFakturRepository->addHutangDetail($request, $autonumberHtg);

                $this->aJurnal->addJurnalHeaderFaktur($request, $notes);
                $this->aJurnal->addJurnalDetailKreditHutangFaktur($request, $rekeningHutang, $autonumberHtg);
                $this->aJurnal->addJurnalDetailDebetHutangBarang($request, $rekHutangbarang->rekening, $autonumberHtg);
                //$this->aJurnal->addJurnalDetailDebetPPNMasukan($request, $rekPPN->rekening, $autonumberHtg);
               // $this->aJurnal->addJurnalDetailDebetDiskonDetil($request, $rekdiskondetil->rekening, $autonumberHtg);
                $this->aJurnal->addJurnalDetailDebetDiskonPembelianLain($request, $rekdiskonpembelianLain->rekening, $autonumberHtg);
                $this->aJurnal->addJurnalDetailDebetBiayaPembelianLain($request, $rekbiayalain->rekening, $autonumberHtg);
            }else{
                $this->aFakturRepository->delHutangDtl($request, $getHutang->first()->KD_HUTANG);
                $this->aFakturRepository->updateHutangHdr($request, $getHutang->first()->KD_HUTANG);
                $this->aJurnal->addJurnalHeaderFaktur($request, $notes);
                $this->aJurnal->addJurnalDetailKreditHutangFaktur($request, $rekeningHutang, $getHutang->first()->KD_HUTANG);
                $this->aJurnal->addJurnalDetailDebetHutangBarang($request, $rekHutangbarang->rekening, $getHutang->first()->KD_HUTANG);
                //$this->aJurnal->addJurnalDetailDebetPPNMasukan($request, $rekPPN->rekening, $getHutang->first()->KD_HUTANG);
                //$this->aJurnal->addJurnalDetailDebetDiskonDetil($request, $rekdiskondetil->rekening, $getHutang->first()->KD_HUTANG);
                $this->aJurnal->addJurnalDetailDebetDiskonPembelianLain($request, $rekdiskonpembelianLain->rekening, $getHutang->first()->KD_HUTANG);
                $this->aJurnal->addJurnalDetailDebetBiayaPembelianLain($request, $rekbiayalain->rekening, $getHutang->first()->KD_HUTANG);
            }
            if($request->TotalTax > 0){
                $includeppn = "1";
            }else{
                $includeppn = "0";
            }
            $this->aFakturRepository->updateFakturHeader($request,$includeppn);
            DB::commit();
            return $this->sendResponse([], 'Items Faktur Add Successfully !  ');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
   
    public function voidFakturDetailbyItem(Request $request)
    {
        // {
        //     // validate 
        //     $request->validate([
        //         "TransactionCode" => "required",
        //         "DateVoid" => "required",
        //         "UserVoid" => "required",
        //         "ReasonVoid" => "required",
        //         "UnitCode" => "required",
        //         "Void" => "required"
        //     ]);
    
        //     try {
        //         // Db Transaction
        //         DB::beginTransaction();
    
        //         // // cek ada gak datanya
        //         if ($this->aFakturRepository->getFakturbyID($request->TransactionCode)->count() < 1) {
        //             return $this->sendError('Faktur Number Not Found !', []);
        //         }

        //         if ($this->aMasterUnitRepository->getUnitById($request->UnitCode)->count() < 1) {
        //             return $this->sendError('Kode Unit tidak ditemukan !', []);
        //         }
    
        //         // BARANG - cek kode barangnya ada ga
        //         if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
        //             return $this->sendError('Kode Barang tidak ditemukan !', []);
        //         } 
    
        //         // Load Data All Do Detil Untuk Di Looping 
        //         //$dtlDo = $this->aFakturRepository->getFakturDetailbyID($request->TransactionCode);
        //         $dtlDo= $this->aFakturRepository->getFakturDetailbyIDandProductCode->first();
     
        //         //foreach ($dtlDo as $key) {
        //             $QtyFaktur = $dtlDo->QtyFaktur;
        //             $valueStok = $this->aDeliveryOrder->getDeliveryOrderDetailByBarang($request->ProductCode, $request->DeliveryCode)->first();
    
        //             $QtyDeliveryRemain = $valueStok->QtyDeliveryRemain; 
        //             $rollbackdo = $QtyDeliveryRemain + $QtyFaktur;
        //             $this->aFakturRepository->editQtyDeliveryOrderRemain($request, $rollbackdo, $request->ProductCode);
        //         //}   

        //         $this->aJurnal->VoidJurnalDtl($request);
        //         //$this->aFakturRepository->VoidHutangHdr($request);
        //         $this->aFakturRepository->voidFakturDetailbyItem($request);
    
        //         DB::commit();
        //         return $this->sendResponse([], 'Faktur Void Detail Successfully !');
        //     } catch (Exception $e) {
        //         DB::rollBack();
        //         Log::info($e->getMessage());
        //         return $this->sendError('Faktur Void Detail Failed !', $e->getMessage());
        //     }
        // }

    }
    public function voidFaktur(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "ReasonVoid" => "required",
            "UnitCode" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // // cek ada gak datanya
            if ($this->aFakturRepository->getFakturbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Faktur Number Not Found !', []);
            }
            if ($this->aFakturRepository->getHutangbyID($request)->first()->KD_ORDER != null || $this->aFakturRepository->getHutangbyID($request)->first()->KD_ORDER != '') {
                return $this->sendError('Hutang sudah ada order pembayaran !', []);
            }
            // Load Data All Do Detil Untuk Di Looping 
            $dtlDo = $this->aFakturRepository->getFakturDetailbyID($request->TransactionCode);
 
            foreach ($dtlDo as $key) {

                $QtyFaktur = $key->QtyFaktur;
                $valueStok = $this->aDeliveryOrder->getDeliveryOrderDetailByBarang($key->ProductCode, $request->DeliveryCode)->first();

                $QtyDeliveryRemain = $valueStok->QtyDeliveryRemain; 
                $rollbackdo = $QtyDeliveryRemain + $QtyFaktur;
                $this->aFakturRepository->editQtyDeliveryOrderRemain($request, $rollbackdo, $key->ProductCode);

                 
            }   
            $this->aJurnal->VoidJurnalHdr($request);
            $this->aJurnal->VoidJurnalDtl($request);
            $this->aFakturRepository->VoidHutangHdr($request);
            $this->aFakturRepository->voidFakturDetailAllOrder($request);
            $this->aFakturRepository->voidFaktur($request);

            DB::commit();
            return $this->sendResponse([], 'Faktur Void Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Faktur Void Failed !', $e->getMessage());
        }
    }
    public function getFakturbyID(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aFakturRepository->getFakturbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Faktur Number Not Found !', []);
            }

            $data = $this->aFakturRepository->getFakturbyID($request->TransactionCode);

            DB::commit();
            return $this->sendResponse($data, 'Faktur Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Faktur Data Not Found !', $e->getMessage());
        }
    }
    public function getFakturDetailbyID(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aFakturRepository->getFakturDetailbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Faktur Not Found !', []);
            }

            $data = $this->aFakturRepository->getFakturDetailbyID($request->TransactionCode);

            DB::commit();
            return $this->sendResponse($data, 'Faktur Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Faktur Data Not Found !', $e->getMessage());
        }
    }
    public function getFakturbyDateUser(Request $request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aFakturRepository->getFakturbyDateUser($request);

            DB::commit();
            return $this->sendResponse($data, 'Faktur Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Faktur Data Not Found !', $e->getMessage());
        }
    }
    public function getFakturbyPeriode(Request $request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aFakturRepository->getFakturbyPeriode($request);

            DB::commit();
            return $this->sendResponse($data, 'Faktur Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Faktur Data Not Found !', $e->getMessage());
        }
    }
}