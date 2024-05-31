<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aJurnalRepositoryImpl;
use App\Http\Repository\aPurchaseOrderImpl;
use App\Http\Repository\aPurchaseOrderRepositoryImpl;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use Exception;
use App\Traits\AutoNumberTrait;

use Illuminate\Support\Str;

class aDeliveryOrderService extends Controller
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

    public function __construct(
        aDeliveryOrderRepositoryImpl $aDeliveryOrder,
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $aSupplierRepository,
        aPurchaseOrderRepositoryImpl $aPurchaseOrderRepository,
        aStokRepositoryImpl $aStok,
        aHnaRepositoryImpl $aHna,
        aJurnalRepositoryImpl $aJurnal
    ) {
        $this->aDeliveryOrder = $aDeliveryOrder;
        $this->aBarangRepository = $aBarangRepository;
        $this->aSupplierRepository = $aSupplierRepository;
        $this->aPurchaseOrderRepository = $aPurchaseOrderRepository;
        $this->aStok = $aStok;
        $this->aHna = $aHna;
        $this->aJurnal = $aJurnal;
    }
    public function addDeliveryOrderHeader(Request $request)
    {
        // validate 
        $request->validate([
            "DeliveryOrderDate" => "required",
            "UserCreate" => "required",
            "SupplierCode" => "required",
            "Notes" => "required",
            "JenisDelivery" => "required",
            "PurchaseOrderCode" => "required",
            "UnitCode" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek supplier kode 
            if ($this->aSupplierRepository->getSupplierbyId($request->SupplierCode)->count() < 1) {
                return $this->sendError('kode Supplier tidak ditemukan !', []);
            }

            // cek kode PR udah ada belom
            if ($this->aPurchaseOrderRepository->getPurchaseOrderbyID($request->PurchaseOrderCode)->count() < 1) {
                return $this->sendError('No. Transaksi Purchase Order tidak ditemukan !', []);
            }

            // // cek ada gak datanya
            $rekHutangbarang = $this->aJurnal->getRekHutangBarang();
            if ($rekHutangbarang->count() < 1) {
                return $this->sendError('Rekening Hutang Barang Kosong, Silahkan Maping Dahulu !', []);
            } 
            // cek rek diskon pembelian detil
            $rekDiskonPembelian = $this->aJurnal->getRekBiayaPembelianlain();
            if ($rekDiskonPembelian->count() < 1) {
                return $this->sendError('Rekening Diskon Pembelian Kosong, Silahkan Maping Dahulu !', []);
            } 

             // cek rek ppn masukan
             $rekPPNMasukan = $this->aJurnal->getRekPPNMasukan();
             if ($rekPPNMasukan->count() < 1) {
                 return $this->sendError('Rekening PPN Masukan Kosong, Silahkan Maping Dahulu !', []);
             } 

            $getmax = $this->aDeliveryOrder->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->DeliveryOrderNumber($request, $TransactionCode);

            $this->aDeliveryOrder->addDeliveryOrder($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Transaksi Delivery Order berhasil di buat, Silahkan masukan data Obat !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addDeliveryOrderDetails(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "UnitCode" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();
            $kodePo = $request->TransactionCode;

            // // cek ada gak datanya
            // if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode)->count() < 1) {
            //     return $this->sendError('Purchase Order Number Not Found !', []);
            // }
            
            // cek udh di faktur belum  
            $cekfaktur = $this->aDeliveryOrder->getFakturbyIDDo($request->TransactionCode);
            if ($cekfaktur->count() > 0) {
                $datafaktur = $cekfaktur->first();
                return $this->sendError('Delivery Order Number Sudah Di Faktur dengan Nomor : ' . $datafaktur->TransactionCode . '!', []);
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

                    if ($key['KonversiQty'] > 1){
                        $konversiqtytotal = $key['Konversi_QtyTotal'];
                    }else{
                        $konversiqtytotal = 1;
                    }
       
                # code...
                // // cek kode barangnya ada ga
                if ($this->aDeliveryOrder->getDeliveryOrderDetailByBarang($key['ProductCode'], $request->TransactionCode)->count() < 1) {
                    // Jika Kosong

                    // cek hpp dulu ada gak
                    $hpp = $this->aHna->getHpp($key,$request);
                    //$nilaiHppBaru = ($key['Price']-$key['DiscountRp'])/$key['Konversi_QtyTotal']; 
                    $nilaiHppBaru = ($key['Price']/$konversiqtytotal-$key['DiscountRp']); 
                 
                   
                    if($hpp->count() > 0 ){   
                        $NilaiHppTerakhir = $hpp->first()->first()->NominalHpp; 
                        $nilaiHppFix = ($NilaiHppTerakhir+$nilaiHppBaru)/2;
                    }else{ 
                        $NilaiHppTerakhir = 0;
                        $nilaiHppFix = $nilaiHppBaru;
                    }
                 
                    $this->aHna->addHpp($request,$key,$nilaiHppFix); 
                    // INSEERT DO DETIL
                    $this->aDeliveryOrder->addDeliveryOrderDetil($key, $kodePo,$nilaiHppFix); 

                    // INSERT BUKU STOK
                    $this->aStok->addBukuStok($request, $key,$nilaiHppFix);
                    $this->aStok->addDataStoks($request, $key,$nilaiHppFix);
                    // UPDATE PURCHASE ORDER DETL QTY PURCAHSE REMAIN
                    $getPoData = $this->aPurchaseOrderRepository->getPurchaseOrderDetailbyIDBrgForDo($request,$key);
                    foreach ($getPoData as $valPo) {
                        $QtyPurchaseRemain = $valPo->QtyPurchaseRemain;
                    }

                    $qtyremainPO = $QtyPurchaseRemain-$key['QtyDelivery']; 
                    
                    $this->aPurchaseOrderRepository->editQtyPurchaseRemain($request,$qtyremainPO, $key['ProductCode']);

                    // update hpp
                    $this->aBarangRepository->editHPPBarang($key,$nilaiHppFix);
                    
                    // // INSERT TABEL STOK
                    // // cek stok ada ga di tabel 
                    // if ($this->aStok->cekStokbyIDBarang($key, $request->UnitCode)->count() < 1) {
                    //     // kalo g ada insert
                    //     $this->aStok->addStok($request, $key);
                    // }else{
                    //     // kallo ada ya update
                    //     $sumstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitCode);
                    //     foreach ($sumstok as $value2 => $value) { 
                    //         $QtyCurrent = $value->Qty; 
                    //     }
                    //     $QtyTotal = $QtyCurrent + $key['Konversi_QtyTotal'];
                    //     $this->aStok->updateStok($request, $key, $QtyTotal); 
                    // }
                    
                    // Update Hna Penjualan
                    if($request->JenisPurchase == "1"){
                        if($request->JenisDeliveryOrder <> "LUAR"){
                            $hnaHigh = $this->aHna->getHnaHigh($key,$request);
                            // $hna = ($key['Price'] / $key['Konversi_QtyTotal']);
                            // $taxconert = $key['TaxRp']/$key['Konversi_QtyTotal'];
                            $hna = ($key['Price'] / $konversiqtytotal);
                            $taxconert = $key['TaxRp']/$konversiqtytotal;
                            $nilaiHnaBaru = $hna + $taxconert;
                            //$discountConvert =  $key['DiscountRp']/$key['Konversi_QtyTotal'];
                            $discountConvert =  $key['DiscountRp']/$konversiqtytotal;
                            $hnaTaxDiskon = (($hna - $discountConvert) + $taxconert );
                            if($hnaHigh->count() < 1 ){ 
                                $nilaiHnaFix = $nilaiHnaBaru;
                            }else{
                                    $NilaiHnaTertingi = $hnaHigh->first()->first()->NominalHna; 
                                
                                if($NilaiHnaTertingi < $nilaiHnaBaru){
                                    $nilaiHnaFix = $nilaiHnaBaru;
                                }else{
                                    $nilaiHnaFix = $NilaiHnaTertingi;
                                }
                            } 
                            $this->aHna->addHna($request, $key,$nilaiHnaFix,$hnaTaxDiskon);
                        } 
                    }
                }else{
                    // Jika Tidak Kosong
                        // cek hpp dulu ada gak
                        
                        $hpp = $this->aHna->getHpp($key,$request);
                        
                        //$nilaiHppBaru = ($key['Price']-$key['DiscountRp'])/$key['Konversi_QtyTotal']; 
                        $nilaiHppBaru = ($key['Price']/$konversiqtytotal-$key['DiscountRp']); 
                        if($hpp->count() > 0  ){ 
                            $NilaiHppTerakhir = $hpp->first()->first()->NominalHpp; 
                            $nilaiHppFix = ($NilaiHppTerakhir+$nilaiHppBaru)/2;
                        
                        }else{
                            $NilaiHppTerakhir = 0;
                            $nilaiHppFix = $nilaiHppBaru;
                        }
                        
                    // INSERT BUKU STOK
                    $tipetrs = "DO";
                    $this->aStok->deleteBukuStok($request,$key, $tipetrs,$request->UnitCode);
                    $this->aStok->deleteDataStoks($request,$key, $tipetrs,$request->UnitCode);
                    $this->aStok->addBukuStok($request, $key,$nilaiHppFix);
                    $this->aStok->addDataStoks($request, $key,$nilaiHppFix);
                    
                    // INSERT TABEL STOK
                    // cek stok ada ga di tabel 
                    // if ($this->aStok->cekStokbyIDBarang($key, $request->UnitCode)->count() < 1) {
                        // kalo g ada insert
                        // $this->aStok->addStok($request, $key);
                    // } else {
                        // kallo ada ya update
                        // $sumstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitCode);
                        // foreach ($sumstok as   $value) { 
                        //     $QtyCurrentStok = $value->Qty; 
                        // }

                        $sumdo = $this->aDeliveryOrder->getDeliveryOrderDetailByBarang($key['ProductCode'], $request->TransactionCode);
                        foreach ($sumdo as  $valDO) { 
                            $QtyDeliveryCurrebt = $valDO->QtyDelivery; 
                        }
                        // $QtyStokBeforeStok = $QtyCurrentStok - $QtyDeliveryCurrebt;
                        // $qtyStokCurrents = $QtyStokBeforeStok + $key['Konversi_QtyTotal'];
                        // $this->aStok->updateStok($request, $key, $qtyStokCurrents); 
                    // }

                    // UPDATE PURCHASE ORDER DETL QTY PURCAHSE REMAIN
                    $getPoData = $this->aPurchaseOrderRepository->getPurchaseOrderDetailbyIDBrgForDo($request, $key);
                    foreach ($getPoData as $valPo) {
                        $QtyPurchaseRemain = $valPo->QtyPurchaseRemain;
                    }

                    $qtyremainPOBefore = $QtyPurchaseRemain + $QtyDeliveryCurrebt;
                    $qtyremainPO = $qtyremainPOBefore - $key['QtyDelivery'];

                    $this->aPurchaseOrderRepository->editQtyPurchaseRemain($request, $qtyremainPO, $key['ProductCode']);

                    // update hpp
                    $this->aBarangRepository->editHPPBarang($key,$nilaiHppFix);

                    // update do detil
                    $this->aDeliveryOrder->updateDeliveryOrdeDetails($request, $key,$nilaiHppFix);


                    // update hna 
                     // Update Hna Penjualan
                     if($request->JenisPurchase == "1"){
                        if($request->JenisDeliveryOrder <> "LUAR"){
                            $hnaHigh = $this->aHna->getHnaHigh($key,$request);
                            // $hna = ($key['Price'] / $key['Konversi_QtyTotal']);
                            // $taxconert = $key['TaxRp']/$key['Konversi_QtyTotal'];
                            $hna = ($key['Price'] / $konversiqtytotal);
                            $taxconert = $key['TaxRp']/$konversiqtytotal;
                            $nilaiHnaBaru = $hna + $taxconert;
                            //$discountConvert =  $key['DiscountRp']/$key['Konversi_QtyTotal'];
                            $discountConvert =  $key['DiscountRp']/$konversiqtytotal;
                            $hnaTaxDiskon = (($hna - $discountConvert) + $taxconert);
                            
                            if($hnaHigh->count() < 1 ){
                                $nilaiHnaFix = $nilaiHnaBaru;
                            }else{ 
                                $NilaiHnaTertingi = $hnaHigh->first()->first()->NominalHna; 
                                if($NilaiHnaTertingi < $nilaiHnaBaru){
                                    $nilaiHnaFix = $nilaiHnaBaru;
                                }else{
                                    $nilaiHnaFix = $NilaiHnaTertingi;
                                }
                            } 
                            $this->aHna->updateHna($request, $key,$nilaiHnaFix,$hnaTaxDiskon);
                        }
                     } 
                }
                
            }
            DB::commit();
           return $this->sendResponse([], 'Barang Delivery Order berhasil ditambahkan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function editDeliveryOrder(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "DeliveryOrderDate" => "required",
            "UserCreate" => "required",
            "UserEdit" => "required",
            "Notes" => "required",
            "Notes1" => "required",
            "Notes2" => "required",
            "TotalQtyDelivery" => "required",
            "SubtotalDelivery" => "required",
            "TaxDelivery" => "required",
            "GrandtotalDelivery" => "required",
            "PurchaseOrderCode" => "required",
            "TotalRowDO" => "required",
            "TotalQtyDO" => "required",
            "JenisDelivery" => "required",
            "UnitCode" => "required",
            "JenisPembayaran" => "required",
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // // cek ada gak datanya
            $rekHutangbarang = $this->aJurnal->getRekHutangBarang();
            if ($rekHutangbarang->count() < 1) {
                return $this->sendError('Rekening Hutang Barang Kosong, Silahkan Maping Dahulu !', []);
            } 
            // cek rek diskon pembelian detil
            $rekDiskonPembelian = $this->aJurnal->getRekBiayaPembelianlain();
            if ($rekDiskonPembelian->count() < 1) {
                return $this->sendError('Rekening Diskon Pembelian Kosong, Silahkan Maping Dahulu !', []);
            } 

            // cek rek ppn masukan
            $rekPPNMasukan = $this->aJurnal->getRekPPNMasukan();
            if ($rekPPNMasukan->count() < 1) {
                return $this->sendError('Rekening PPN Masukan Kosong, Silahkan Maping Dahulu !', []);
            } 

            if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Delivery Order tidak ditemukan !', []);
            } 
           
            $this->aJurnal->delJurnalHdr($request);
            $this->aJurnal->delJurnalDtl($request);

            $cekDodetil = $this->aDeliveryOrder->getDeliveryOrderDetailbyID($request->TransactionCode);
            foreach ($cekDodetil as $valueDo) { 
                $cekrek = $this->aJurnal->getRekeningPersediaaan($valueDo->ProductCode); 
                if($cekrek->count() < 1) {
                    return $this->sendError('Rekening Persediaan Kosong, Silahkan Cek Master Kelompok dan Barang Anda !', []);
                } 
                 
            }
            
            foreach ($cekDodetil as $valueDo) { 

                $cekrek = $this->aJurnal->getRekeningPersediaaan($valueDo->ProductCode); 
                //test needed
                //$valueDo['UnitCode'] = $request->UnitCode;
                $this->aJurnal->addJurnalDetailDebetPersediaan($valueDo, $cekrek->first()->RekPersediaan); 

                if($valueDo->DiscountRpTTL > 0 ){
                    $this->aJurnal->addJurnalDetailDiskonDetilDO($valueDo,$rekDiskonPembelian->first()->rekening );
                }

                if($valueDo->TaxRpTTL > 0 ){
                    $this->aJurnal->addJurnalDetailDebetPPNMasukanDO($valueDo,$rekPPNMasukan->first()->rekening );
                }
                
            }

            $this->aJurnal->addJurnalDetailKreditHutangBarang($request, $rekHutangbarang->first()->rekening);
            $notes = 'Pembelian Barang No. Penerimaan : ' . $request->TransactionCode;
            $this->aJurnal->addJurnalHeader($request, $notes);
            $this->aDeliveryOrder->editDeliveryOrder($request);
            DB::commit();
            return $this->sendResponse([], 'Transaksi Delivery Order Berhasil di Simpan !');

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Gagal di Proses!', $e->getMessage());
        }
    }
    public function voidDeliveryOrder(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "PurchaseCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "ReasonVoid" => "required",
            "UnitCode" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek udh di faktur belum
            $cekfaktur = $this->aDeliveryOrder->getFakturbyIDDo($request->TransactionCode);
            if ($cekfaktur->count()> 0) {
                $datafaktur = $cekfaktur->first();
                return $this->sendError('Delivery Order Number Sudah Di Faktur dengan Nomor : ' . $datafaktur->TransactionCode . '!', []);
            }

            // // // cek ada gak datanya
            if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Delivery Order tidak ditemukan !', []);
            }

            // Validasi Stok
            $dtlDo = $this->aDeliveryOrder->getDeliveryOrderDetailbyID($request->TransactionCode);
           
            foreach ($dtlDo as $value) {
                $Konversi_QtyTotal = $value->Konversi_QtyTotal;
                $cekqtystok = $this->aStok->cekStokbyIDBarangOnly($value->ProductCode, $request);
               
                foreach ($cekqtystok as $valueStok) {
                    $datastok = $valueStok->Qty;
                }
               
                $sisaStok = $datastok - $Konversi_QtyTotal;
            
                if($sisaStok < 0 ){
                    return $this->sendError('Barang ' . $valueStok->NamaBarang . ' Stok Invalid, Void Delivery Order Dibatalkan !', []);
                }
            }

            // Load Data All Do Detil Untuk Di Looping 
            $dtlDo2 = $this->aDeliveryOrder->getDeliveryOrderDetailbyID($request->TransactionCode);
            $reff_void = 'DO_V';
          
            foreach ($dtlDo2 as $key2) {

                $QtyDelivery = $key2->QtyDelivery;
                $Konversi_QtyTotal = $key2->Konversi_QtyTotal;
                // $cekqtystok = $this->aStok->cekStokbyIDBarangOnly($key2->ProductCode,$request);
                    // foreach ($cekqtystok as $valueStok) {
                    //     $datastok = $valueStok->Qty;
                    // }
                // $sisaStok = $datastok - $Konversi_QtyTotal;
                // $this->aStok->updateStokPerItemBarang($request, $key2->ProductCode, $sisaStok);
                $this->aStok->addBukuStokVoid($request, $key2,$reff_void);
                $this->aStok->addDataStoksVoid($request, $key2,$reff_void);
                // update nilai Hpp ke No. Trs Do Sebelumnya
                $cekDoTerakhirHna = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDnotIdTrsNow($key2, $request);
               
                if( $cekDoTerakhirHna->count() < 1){
                    $hna = "0";
                    // jika tidak di temukan do terakhir update hpp jadi 0
                    $this->aBarangRepository->editHPPBarangDoVoidNull($hna, $key2->ProductCode);
                }else{
                    $hna = $cekDoTerakhirHna->first();
                    $this->aBarangRepository->editHPPBarangDoVoidNotNull($hna);
                }

                // Update Purchase Order Qty Remain  
                $getPoData = $this->aPurchaseOrderRepository->getPurchaseOrderDetailbyIDBrgForDo2($request, $key2->ProductCode);
                foreach ($getPoData as $valPo) {
                    $QtyPurchaseRemain = $valPo->QtyPurchaseRemain;
                }

                $qtyremainPO = $QtyPurchaseRemain + $key2->QtyDelivery;
                $this->aBarangRepository->updateBatalHnabyDo($request);
                $this->aBarangRepository->updateBatalHppbyDo($request);
                $this->aPurchaseOrderRepository->editQtyPurchaseRemain($request, $qtyremainPO, $key2->ProductCode);

            }
            
            //insert log
            $getDataTrs = $this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode)->first();
            $this->aBarangRepository->insertLog('DELIVERY ORDER',$request->TransactionCode,$getDataTrs->NamaUser,$request->ReasonVoid);
           
            $this->aDeliveryOrder->voidDeliveryOrderDetailAllOrder($request);
            $this->aDeliveryOrder->voidDeliveryOrder($request);


            DB::commit();
            return $this->sendResponse([], 'Transaksi Delivery Order berhasil dibatalkan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaksi tidak dapat di Proses !', $e->getMessage());
        }
    }
    public function voidDeliveryOrderDetailbyItem(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required",
            "PurchaseCode" => "required",
            "ProductCode" => "required",
            "UnitCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();
            // cek udh di faktur belum  
            $cekfaktur = $this->aDeliveryOrder->getFakturbyIDDo($request->TransactionCode);
            if ($cekfaktur->count() > 0) {
                $datafaktur = $cekfaktur->first();
                return $this->sendError('Delivery Order Number Sudah Di Faktur dengan Nomor : ' . $datafaktur->TransactionCode . '!', []);
            }

            // // cek ada gak datanya 
            if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Delivery Order Number Not Found !', []);
            }
            // cek aktif engga
            $cekdodetil = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDandProductCode($request)->count();
            if ($cekdodetil < 1) {
                return $this->sendError('Kode Barang Sudah di Batalkan !', []);
            }
            // Validasi Stok - cek stok dulu
                $dtlDo = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDandProductCode($request)->first();
                $QtyDelivery = $dtlDo->Konversi_QtyTotal;
                $cekqtystok = $this->aStok->cekStokbyIDBarangOnly($dtlDo->ProductCode, $request)->first();
                $datastok = $cekqtystok->Qty;
                $sisaStok = $datastok - $QtyDelivery;
                if ($sisaStok < 0) {
                    return $this->sendError('Barang ' . $cekqtystok->NamaBarang . ' Stok Invalid, Void Delivery Order Dibatalkan !', []);
                }
               
                // $this->aStok->updateStokPerItemBarang($request, $dtlDo->ProductCode, $sisaStok);
                $this->aStok->addBukuStokVoidbyIdProduct($request, $dtlDo);
                $this->aStok->addDataStokVoidbyIdProduct($request, $dtlDo);
                // update nilai Hpp ke No. Trs Do Sebelumnya
                $cekDoTerakhirHna = $this->aDeliveryOrder->getDeliveryOrderDetailbyIDnotIdTrsNow($dtlDo, $request);

                if ($cekDoTerakhirHna->count() < 1) {
                    $hna = '0';
                    // jika tidak di temukan do terakhir update hpp jadi 0
                    $this->aBarangRepository->editHPPBarangDoVoidNull($hna,$request->ProductCode);
                } else {
                    $hna = $cekDoTerakhirHna->first();
                    $this->aBarangRepository->editHPPBarangDoVoidNotNull($hna);
                }

                $this->aDeliveryOrder->voidDeliveryOrderDetailbyItem($request);

                // Update Purchase Order Qty Remain 
                $getPoData = $this->aPurchaseOrderRepository->getPurchaseOrderDetailbyIDBrgForDo2($request, $request->ProductCode);
              
                foreach ($getPoData as $valPo) {
                    $QtyPurchaseRemain = $valPo->QtyPurchaseRemain;
                }

                $qtyremainPO = $QtyPurchaseRemain + $dtlDo->QtyDelivery;

                $this->aPurchaseOrderRepository->editQtyPurchaseRemain($request, $qtyremainPO, $request->ProductCode);

            DB::commit();

            return $this->sendResponse([], 'Transaksi Delivery Order Detail Berhasil dibatalkan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaksi tidak dapat diproses !', $e->getMessage());
        }
    }
    public function getDeliveryOrderbyID($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);
        try {
            // cek ada gak datanya
            if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Delivery Order tidak di ditemukan !', []);
            } 
            $data = $this->aDeliveryOrder->getDeliveryOrderbyID($request->TransactionCode);
            return $this->sendResponse($data, 'No. Transaksi Delivery Order di ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Transaksi tidak dapat di proses !', $e->getMessage());
        }
    }
    public function getDeliveryOrderDetailbyID($request)
    {
        // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);

        try { 
            // cek ada gak datanya
            if ($this->aDeliveryOrder->getDeliveryOrderDetailbyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('No. Transaksi Delivery Order tidak di ditemukan ! !', []);
            }
            $data = $this->aDeliveryOrder->getDeliveryOrderDetailbyID($request->TransactionCode);
            return $this->sendResponse($data, 'No. Transaksi Delivery Order di ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Transaksi tidak dapat di proses !', $e->getMessage());
        }
    }
    public function getDeliveryOrderbyDateUser($request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);

        try { 
            $data = $this->aDeliveryOrder->getDeliveryOrderbyDateUser($request);
            return $this->sendResponse($data, 'No. Transaksi Delivery Order di ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Transaksi tidak dapat di proses !', $e->getMessage());
        }
    }
    public function getDeliveryOrderbyPeriode($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);

        try {
            $data = $this->aDeliveryOrder->getDeliveryOrderbyPeriode($request);
            return $this->sendResponse($data, 'No. Transaksi Delivery Order di ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Delivery Order Data Not Found !', $e->getMessage());
        }
    }

    public function getCalculateDateTempobyIDSupplier($request)
    {
        // validate 
        $request->validate([
            "TransasctionDate" => "required",
            "IDSupplier" => "required",
        ]);

        try {
            $datatempo = $this->aSupplierRepository->getSupplierbyId($request->IDSupplier)->first()->LamaJatuhTempo_Days;
            // cek ada gak datanya
            if ($datatempo == null) {
                return $this->sendError('Tanggal jatuh tempo di master supplier masih kosong ! Mohon dilengkapi untuk kalkulasi tanggal jatuh tempo !', []);
            }

            //calulcate
            $datetrs = date('Y-m-d', strtotime($request->TransasctionDate));
            $getdatetempo = date('Y-m-d', strtotime($datetrs. ' + '.$datatempo.' days'));

            return $this->sendResponse($getdatetempo, 'Tanggal jatuh tempo ditemukan !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Delivery Order Data Not Found !', $e->getMessage());
        }
    }
}
