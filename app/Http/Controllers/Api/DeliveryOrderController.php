<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\aDeliveryOrderService;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aJurnalRepositoryImpl;
use App\Http\Repository\aPurchaseOrderRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;

class DeliveryOrderController extends Controller
{
    //
    public function adddeliveryOrder(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $addHeader =  $aPurchaseOrderService->addDeliveryOrderHeader($request);
        return $addHeader;
    }
    public function addDeliveryOrderDetails(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->addDeliveryOrderDetails($request);
        return $adddetail;
    }
    public function editDeliveryOrder(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->editDeliveryOrder($request);
        return $adddetail;
    }
    public function getDeliveryOrderbyID(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->getDeliveryOrderbyID($request);
        return $adddetail;
    }
    public function getDeliveryOrderDetailbyID(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->getDeliveryOrderDetailbyID($request);
        return $adddetail;
    }
    public function getDeliveryOrderbyDateUser(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->getDeliveryOrderbyDateUser($request);
        return $adddetail;
    }
    public function getDeliveryOrderbyPeriode(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->getDeliveryOrderbyPeriode($request);
        return $adddetail;
    }
    public function voidDeliveryOrder(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->voidDeliveryOrder($request);
        return $adddetail;
    }
    public function voidDeliveryOrderDetailbyItem(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->voidDeliveryOrderDetailbyItem($request);
        return $adddetail;
    }
    public function getCalculateDateTempobyIDSupplier(Request $request)
    {
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseOrderRepository = new aPurchaseOrderRepositoryImpl();
        $sStok = new aStokRepositoryImpl();
        $aHna = new aHnaRepositoryImpl();
        $aJurnal = new aJurnalRepositoryImpl();
        $aPurchaseOrderService = new aDeliveryOrderService(
            $aDeliveryOrderRepository,
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseOrderRepository,
            $sStok,
            $aHna,
            $aJurnal
        );
        $adddetail =  $aPurchaseOrderService->getCalculateDateTempobyIDSupplier($request);
        return $adddetail;
    }
}
