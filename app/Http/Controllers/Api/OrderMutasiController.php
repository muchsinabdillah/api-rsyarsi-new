<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aOrderMutasiRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Service\aOrderMutasiService;

class OrderMutasiController extends Controller
{
    //
    public function addOrderMutasi(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->addOrderMutasi($request);
        return $execute;
    }
    public function addOrderMutasiDetail(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->addOrderMutasiDetail($request);
        return $execute;
    }
    public function editOrderMutasi(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->editOrderMutasi($request);
        return $execute;

    }
    public function voidOrderMutasi(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->voidOrderMutasi($request);
        return $execute;

    }
    public function voidOrderMutasiDetailbyItem(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->voidOrderMutasiDetailbyItem($request);
        return $execute;
    }
    public function getOrderMutasibyID(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->getOrderMutasibyID($request);
        return $execute;
    }
    public function getOrderMutasiDetailbyID(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->getOrderMutasiDetailbyID($request);
        return $execute;
    }
    public function getOrderMutasiDetailRemainbyID(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->getOrderMutasiDetailRemainbyID($request);
        return $execute;
    }
    public function getOrderMutasibyDateUser(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->getOrderMutasibyDateUser($request);
        return $execute;
    }
    public function getOrderMutasibyPeriode(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->getOrderMutasibyPeriode($request);
        return $execute;
    }
    public function approval(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->approval($request);
        return $execute;
    }
    public function addOrderMutasiDetailPaket(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $aPurchaseRequestRepository = new aPurchaseRequisitionRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aOrderMutasiRepository = new aOrderMutasiRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $aHnaRepository = new aHnaRepositoryImpl();
        $aOrderMutasiService = new aOrderMutasiService(
            $aBarangRepository,
            $asupplierRepository,
            $aPurchaseRequestRepository,
            $aStokRepository,
            $aOrderMutasiRepository,
            $aMasterUnitRepository,
            $aHnaRepository 
        );
        $execute =  $aOrderMutasiService->addOrderMutasiDetailPaket($request);
        return $execute;
    }
}
