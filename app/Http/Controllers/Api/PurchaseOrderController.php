<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\aPurchaseOrderService;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aPurchaseOrderRepositoryImpl;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;

class PurchaseOrderController extends Controller
{
    //
    public function addPurchaseOrder(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService($aPurchaseOrderRepository, $aBarangRepository, 
                                                            $asupplierRepository, $aPurchaseRequestRepository);
        $addHeader =  $aPurchaseOrderService->addPurchaseOrderHeader($request);
        return $addHeader;
    }
    public function addPurchaseOrderDetails(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $adddetail =  $aPurchaseOrderService->addPurchaseOrderDetil($request);
        return $adddetail;
    }
    public function editPurchaseOrder(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->editPurchaseOrder($request);
        return $edit;
    }

    public function voidPurchaseOrder(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->voidPurchaseOrder($request);
        return $edit;
    }
    public function voidPurchaseOrderDetailbyItem(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->voidPurchaseOrderDetailbyItem($request);
        return $edit;
    }
    public function getPurchaseOrderbyID(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->getPurchaseOrderbyID($request);
        return $edit;
    }
    public function getPurchaseOrderDetailbyID(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->getPurchaseOrderDetailbyID($request);
        return $edit;
    }
    public function getPurchaseOrderbyDateUser(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->getPurchaseOrderbyDateUser($request);
        return $edit;
    }
    public function getPurchaseOrderbyPeriode(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->getPurchaseOrderbyPeriode($request);
        return $edit;
    }
    public function approvalFirst(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->approvalFirst($request);
        return $edit;
    }
    public function approvalSecond(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->approvalSecond($request);
        return $edit;
    }
    public function approvalThirth(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->approvalThirth($request);
        return $edit;
    }
    public function closePurchaseOrder(Request $request)
    {
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aPurchaseOrderService = new aPurchaseOrderService(
            $aPurchaseOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository
        );
        $edit =  $aPurchaseOrderService->closePurchaseOrder($request);
        return $edit;
    }
}
