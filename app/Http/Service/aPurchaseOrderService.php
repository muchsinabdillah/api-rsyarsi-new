<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl; 
use App\Http\Repository\aPurchaseOrderImpl;
use App\Http\Repository\aPurchaseOrderRepositoryImpl;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use Exception;
use App\Traits\AutoNumberTrait;

use Illuminate\Support\Str;

class aPurchaseOrderService extends Controller
{
    use AutoNumberTrait;
    private $aPurchaseOrder;
    private $aBarangRepository;
    private $aSupplierRepository;
    private $aPurchaseRequisitionRepository;

    public function __construct(
        aPurchaseOrderRepositoryImpl $aPurchaseOrder,
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $aSupplierRepository,
        aPurchaseRequisitionRepositoryImpl $aPurchaseRequisitionRepository

    ) {
        $this->aPurchaseOrder = $aPurchaseOrder;
        $this->aBarangRepository = $aBarangRepository;
        $this->aSupplierRepository = $aSupplierRepository;
        $this->aPurchaseRequisitionRepository = $aPurchaseRequisitionRepository;
    }
    public function addPurchaseOrderHeader(Request $request)
    {
        // validate 
        $request->validate([
            "PurchaseDate" => "required",
            "UserCreate" => "required",
            "SupplierCode" => "required",
            "Notes" => "required",
            "PurchaseReqCode" => "required" 
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek supplier kode 
            if ($this->aSupplierRepository->getSupplierbyId($request->SupplierCode)->count() < 1) {
                return $this->sendError('Supplier Code Not Found !', []);
            }

            // cek kode PR udah ada belom
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->PurchaseReqCode)->count() < 1) {
                return $this->sendError('Purchase Order Code Not Found !', []);
            }

            $getmax = $this->aPurchaseOrder->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->PurchaseCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->PurchaseOrderNumber($request, $TransactionCode);
            
            $this->aPurchaseOrder->addPurchaseOrder($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Create Purchase Order Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addPurchaseOrderDetil(Request $request)
    {
        // validate 
        $request->validate([
            "PurchaseCode" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();
            $kodePo = $request->PurchaseCode;

            // // cek ada gak datanya
            if ($this->aPurchaseOrder->getPurchaseOrderbyID($request->PurchaseCode)->count() < 1) {
                return $this->sendError('Purchase Order Number Not Found !', []);
            }
            // // cek sudah di approved belum 
            if ($this->aPurchaseOrder->getPurchaseOrderApprovedbyID($request->PurchaseCode)->count() > 0) {
                return $this->sendError('Purchase Order Number Has Been Approved !', []);
            }

            foreach ($request->Items as $key) {
                # code...
                // // cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                    return $this->sendError('Product Not Found !', []);
                }
            }

            foreach ($request->Items as $key ) {
                # code...
                // update qty Pr ke Awal dulu 
                // update Qty Remain
                if($this->aPurchaseOrder->getPurchaseOrderDetailbyIDBrgForDo($request, $key)->count() < 1 ){
                    $getPr = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionDetailbyIDBarang($request, $key);
                    foreach ($getPr as $valPo) {
                        $QtyQtyRemainPR = $valPo->QtyRemainPR;
                    }
                    $qtyremainPRAfter = $QtyQtyRemainPR - $key['QtyPurchase']; 
                }else{
                    $getPOData = $this->aPurchaseOrder->getPurchaseOrderDetailbyIDBrgForDo($request, $key);
                    
                    foreach ($getPOData as $valDataPO) {
                        $QtyPOawal= $valDataPO->QtyPurchase;
                    }
                    $getPr = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionDetailbyIDBarang($request, $key);
                    foreach ($getPr as $valPo) {
                        $QtyQtyRemainPR = $valPo->QtyRemainPR;
                    }
                    $qtyremainPRBefore = $QtyPOawal + $QtyQtyRemainPR;
                    $qtyremainPRAfter = $qtyremainPRBefore - $key['QtyPurchase']; 
                }
                // Hapus PO detil per item
                $this->aPurchaseOrder->delItemsbyPOnumber($key, $kodePo);
                // add detail PO
                $this->aPurchaseOrder->addPurchaseOrderDetil($key, $kodePo); 
                $this->aPurchaseRequisitionRepository->updateQtyRemainPR($request,$key,$qtyremainPRAfter);

            }  

            DB::commit();
            return $this->sendResponse([], 'Items Purchase Order Add Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function editPurchaseOrder(Request $request)
    {
        // validate 
        $request->validate([
            "PurchaseCode" => "required",
            "PurchaseDate" => "required",
            "UserCreate" => "required",
            "UserEdit" => "required", 
            "Notes" => "required",
            "Notes1" => "required",
            "Notes2" => "required",
            "TotalQtyPurchase" => "required",
            "SubtotalPurchase" => "required",
            "TaxPurchase" => "required",
            "GrandtotalPurchase" => "required",
            "PurchaseReqCode" => "required",
            "Close_PO" => "required",
            "TotalRowPO" => "required",
            "TipePO" => "required" 
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // // cek ada gak datanya
            if ($this->aPurchaseOrder->getPurchaseOrderbyID($request->PurchaseCode)->count() < 1) {
                return $this->sendError('Purchase Order Number Not Found !', []);
            }
            // // cek sudah di approved belum 
            if ($this->aPurchaseOrder->getPurchaseOrderApprovedbyID($request->PurchaseCode)->count() > 0) {
                return $this->sendError('Purchase Order Number Has Been Approved !', []);
            }

            $this->aPurchaseOrder->editPurchaseOrder($request);

            DB::commit();
            return $this->sendResponse([], 'Purchase Order Edited Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Edited Failed !', $e->getMessage());
        }
    }
    public function voidPurchaseOrder(Request $request)
    {
        // validate 
        $request->validate([
            "PurchaseCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "PurchaseRequisitonCode" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // // cek ada gak datanya
            if ($this->aPurchaseOrder->getPurchaseOrderbyID($request->PurchaseCode)->count() < 1) {
                return $this->sendError('Purchase Order Number Not Found !', []);
            }
            if ($this->aPurchaseOrder->getPurchaseOrderDetailbyID($request->PurchaseCode)->count() < 1) {
                return $this->sendError('Purchase Order Detail Number Not Found !', []);
            }
            // // cek sudah di approved belum 
            if ($this->aPurchaseOrder->getPurchaseOrderApprovedbyID($request->PurchaseCode)->count() > 0) {
                return $this->sendError('Purchase Order Number Has Been Approved !', []);
            }
            // if ($this->aPurchaseOrder->getPurchaseOrderDetailbyID($request->PurchaseCode)->count() < 1) {
            //     return $this->sendError('Purchase Order Detail Number Not Found !', []);
            // }

            
            // void detail pr - reset qty order
            $getPodetil = $this->aPurchaseOrder->getPurchaseOrderDetailbyID($request->PurchaseCode);
           
            foreach ($getPodetil as $valueDodetil) {
                $QtyPurchase = $valueDodetil->QtyPurchase;
                $ProductCode = $valueDodetil->ProductCode;
                    $getPr = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionDetailPObyIDBarang2($request->PurchaseRequisitonCode,$ProductCode);
                    
                    if ($getPr->count() < 1) {
                        
                        return $this->sendError('Purchase Requisition Detail Not Found !', []);
                    } 
                    $QtyQtyRemainPR = $getPr->first()->QtyRemainPR;
                    $qtyremainPRAfter = $QtyQtyRemainPR + $QtyPurchase; 
                    $this->aPurchaseRequisitionRepository->updateQtyRemainPRbyPo2($request->PurchaseRequisitonCode,$ProductCode,$qtyremainPRAfter);
            }

            // void detail pr - reset qty order
            $this->aPurchaseOrder->voidPurchaseDetailAllOrder($request);
            $this->aPurchaseOrder->voidPurchaseOrder($request);

            DB::commit();
            return $this->sendResponse([], 'Purchase Order Void Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Void Failed !', $e->getMessage());
        }
    }
    public function voidPurchaseOrderDetailbyItem(Request $request)
    {
        // validate 
        $request->validate([
            "PurchaseCode" => "required",
            "ProductCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "QtyPurchase" => "required",
            "PurchaseRequisitonCode" => "required",
            "Void" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // // cek ada gak datanya
            if ($this->aPurchaseOrder->getPurchaseOrderbyID($request->PurchaseCode)->count() < 1) {
                return $this->sendError('Purchase Order Number Not Found !', []);
            }
            // // cek sudah di approved belum 
            if ($this->aPurchaseOrder->getPurchaseOrderApprovedbyID($request->PurchaseCode)->count() > 0) {
                return $this->sendError('Purchase Order Number Has Been Approved !', []);
            }
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionDetailPObyIDBarang($request)->count() < 1) {
                return $this->sendError('Purchase Order with This Code Item Invalid !', []);
            }
            $this->aPurchaseOrder->voidPurchaseOrderDetailbyItem($request);

            $getPr = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionDetailPObyIDBarang($request)->first();
          
            $QtyQtyRemainPR = $getPr->QtyRemainPR;
            $qtyremainPRAfter = $QtyQtyRemainPR + $request['QtyPurchase']; 
    
            $this->aPurchaseRequisitionRepository->updateQtyRemainPRbyPo($request,$qtyremainPRAfter);

            DB::commit();
            return $this->sendResponse([], 'Purchase Order Void Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Void Failed !', $e->getMessage());
        }
    }
    public function getPurchaseOrderbyID($request)
    {
        // validate 
        $request->validate([
            "PurchaseCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aPurchaseOrder->getPurchaseOrderbyID($request->PurchaseCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aPurchaseOrder->getPurchaseOrderbyID($request->PurchaseCode);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Order Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Order Data Not Found !', $e->getMessage());
        }
    }
    public function getPurchaseOrderDetailbyID($request)
    {
        // validate 
        $request->validate([
            "PurchaseCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aPurchaseOrder->getPurchaseOrderDetailbyID($request->PurchaseCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aPurchaseOrder->getPurchaseOrderDetailbyID($request->PurchaseCode);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Order Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Requisition Data Not Found !', $e->getMessage());
        }
    }
    public function getPurchaseOrderbyDateUser($request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aPurchaseOrder->getPurchaseOrderbyDateUser($request);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Order Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Order Data Not Found !', $e->getMessage());
        }
    }
    public function getPurchaseOrderbyPeriode($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aPurchaseOrder->getPurchaseOrderbyPeriode($request);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Order Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Order Data Not Found !', $e->getMessage());
        }
    }
}
