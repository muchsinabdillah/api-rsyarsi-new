<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\aGroupRepositoryImpl;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;
use Exception;
use App\Traits\AutoNumberTrait;

use Illuminate\Support\Str; 

class aPurchaseRequisitionService extends Controller
{
    use AutoNumberTrait;
    private $aPurchaseRequisitionRepository;
    private $aBarangRepository;

    public function __construct(
        aPurchaseRequisitionRepositoryImpl $aPurchaseRequisitionRepository,
        aBarangRepositoryImpl $aBarangRepository
        
        )
    {
        $this->aPurchaseRequisitionRepository = $aPurchaseRequisitionRepository;
        $this->aBarangRepository = $aBarangRepository;
    }
    public function addPurchaseRequisition(Request $request)
    {
        // validate 
        $request->validate([
            "TransasctionDate" => "required",
            "UserCreate" => "required",
            "Type" => "required",
            "Unit" => "required",
            "Notes" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();
            $getmax = $this->aPurchaseRequisitionRepository->getMaxCode($request);
            if( $getmax->count() > 0){
                    foreach ($getmax as $datanumber) {
                        $TransactionCode = $datanumber->TransactionCode;
                    }
            }else{
                $TransactionCode = 0;
            }
            $autonumber = $this->PurchaseRequisitionNumber($request, $TransactionCode);
            $this->aPurchaseRequisitionRepository->addPurchaseRequisition($request, $autonumber); 
            DB::commit();  
            return $this->sendResponse($autonumber, 'Create Purchase Requisition Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addPurchaseRequisitionDetil(Request $request){
        // validate 
        $request->validate([
            "TransasctionCode" => "required",
            "ProductCode" => "required",
            "ProductName" => "required",
            "QtyStok" => "required",
            "QtyPR" => "required",
            "Satuan" => "required",
            "Satuan_Konversi" => "required",
            "KonversiQty" => "required",
            "Konversi_QtyTotal" => "required",
            "UserAdd" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction(); 

            // cek ada gak datanya
            if($this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->TransasctionCode)->count() < 1 ){
                return $this->sendError('Transaction Number Not Found !',[]);
            }
            // cek kode barangnya ada ga
            if($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1){
                return $this->sendError('Product Not Found !', []);
            }
            // cek Konversi nya udah belom
            $konversi = $this->aBarangRepository->getBarangbyId($request->ProductCode)->first();
            if ($konversi->Konversi_satuan  < 1) {
                return $this->sendError('Konversi Satuan Invalid, Silahkan Masukan Konversi Satuan !', []);
            }
            //cek barang dobel gak 
            if($this->aPurchaseRequisitionRepository->getItemsDouble($request)->count() > 0 ){
                return $this->sendError('Product Code Double !', []);
            }
            // cek sudah di approved belum 
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionApprovedbyID($request->TransasctionCode)->count() > 0) {
                return $this->sendError('Transaction Number Has Been Approved !', []);
            }
            $this->aPurchaseRequisitionRepository->addPurchaseRequisitionDetil($request);
            
            DB::commit();
            return $this->sendResponse([], 'Items Purchase Requisition Add Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }

    }
    public function editPurchaseRequisition(Request $request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "TotalQty" => "required",
            "TotalRow" => "required" ,
            "Notes" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            } 
            // cek sudah di approved belum 
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionApprovedbyID($request->TransasctionCode)->count() > 0) {
                return $this->sendError('Transaction Number Has Been Approved !', []);
            }
            if($request->TotalRow < 1){
                return $this->sendError('There is No Items, Edited Cancelled !', []);
            }
            if ($request->TotalQty < 1) {
                return $this->sendError('There is No Qty Items, Edited Cancelled !', []);
            }
            $this->aPurchaseRequisitionRepository->editPurchaseRequisition($request);

            DB::commit();
            return $this->sendResponse([], 'Purchase Requisition Edited Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Edited Failed !', $e->getMessage());
        }
    }
    public function voidPurchaseRequisition(Request $request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }
            // cek sudah di approved belum 
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionApprovedbyID($request->TransasctionCode)->count() > 0) {
                return $this->sendError('Transaction Number Has Been Approved !', []);
            }
            
            $this->aPurchaseRequisitionRepository->voidPurchaseRequisitionDetailAll($request);
            $this->aPurchaseRequisitionRepository->voidPurchaseRequisition($request);

            DB::commit();
            return $this->sendResponse([], 'Purchase Requisition Void Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Void Failed !', $e->getMessage());
        }
    }
    public function voidPurchaseRequisitionDetailbyItem(Request $request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required",
            "ProductCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            // cek sudah di approved belum 
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionApprovedbyID($request->TransasctionCode)->count() > 0) {
                return $this->sendError('Transaction Number Has Been Approved !', []);
            }
            
            $this->aPurchaseRequisitionRepository->voidPurchaseRequisitionDetailbyItem($request);

            DB::commit();
            return $this->sendResponse([], 'Purchase Requisition Void Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Void Failed !', $e->getMessage());
        }
    }
    public function getPurchaseRequisitionbyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required" 
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->TransasctionCode);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Requisition Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Requisition Data Not Found !', $e->getMessage());
        }
    }
    public function getPurchaseRequisitionDetailbyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionDetailbyID($request->TransasctionCode);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Requisition Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Requisition Data Not Found !', $e->getMessage());
        }
    }
    public function getPurchaseRequisitionbyDateUser($request)
    {
        // validate 
        $request->validate([ 
            "UserCreate" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction(); 
            
            $data = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyDateUser($request);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Requisition Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Requisition Data Not Found !', $e->getMessage());
        }
    }
    public function getPurchaseRequisitionbyPeriode($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aPurchaseRequisitionRepository->getPurchaseRequisitionbyPeriode($request);

            DB::commit();
            return $this->sendResponse($data, 'Purchase Requisition Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Purchase Requisition Data Not Found !', $e->getMessage());
        }
    }
}
