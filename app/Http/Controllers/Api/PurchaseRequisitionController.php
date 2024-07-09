<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Service\aPurchaseRequisitionService;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;

class PurchaseRequisitionController extends Controller
{
    //
    public function addPurchaseRequisition(Request $request){
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->addPurchaseRequisition($request);
        return $addGroup;
    }
    public function addPurchaseRequisitionDetil(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->addPurchaseRequisitionDetil($request);
        return $addGroup;
    }
    public function editPurchaseRequisition(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->editPurchaseRequisition($request);
        return $addGroup;
    }
    public function voidPurchaseRequisition(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->voidPurchaseRequisition($request);
        return $addGroup;

    }
    public function voidPurchaseRequisitionDetailbyItem(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->voidPurchaseRequisitionDetailbyItem($request);
        return $addGroup;
    }
    public function getPurchaseRequisitionbyID(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->getPurchaseRequisitionbyID($request);
        return $addGroup;
    }
    public function getPurchaseRequisitionDetailbyID(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->getPurchaseRequisitionDetailbyID($request);
        return $addGroup;
    }
    public function getPurchaseRequisitionbyDateUser(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->getPurchaseRequisitionbyDateUser($request);
        return $addGroup;
    }
    public function getPurchaseRequisitionbyPeriode(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->getPurchaseRequisitionbyPeriode($request);
        return $addGroup;
    }
    public function approval(Request $request)
    {
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->approval($request);
        return $addGroup;
    }
    public function updateDetailKonversiPoPR(Request $request){
        $aPurchaseRequisitionRepository = new aPurchaseRequisitionRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $aPurchaseRequisitionService = new aPurchaseRequisitionService($aPurchaseRequisitionRepository, $aBarangRepository);
        $addGroup =  $aPurchaseRequisitionService->updateDetailKonversiPoPR($request);
        return $addGroup;
    }
}
