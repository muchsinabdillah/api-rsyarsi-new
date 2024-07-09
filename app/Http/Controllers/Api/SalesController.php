<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Service\aSalesService;
use App\Http\Controllers\Controller;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aSalesRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aTrsResepRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;
use App\Http\Repository\bBillingRepositoryImpl;

class SalesController extends Controller
{
    //
    public function addSalesHeader(Request $request){
      
        $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->addSalesHeader($request);
        return $add; 

    }
    public function addSalesDetail(Request $request){
      
        $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->addSalesDetail($request);
        return $add; 

    }
    public function voidSales(Request $request){
      
                $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->voidSales($request);
        return $add; 

    }
    public function voidSalesDetailbyItem(Request $request){
      
                $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->voidSalesDetailbyItem($request);
        return $add; 

    }
    public function finishSalesTransaction(Request $request){
      
                $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->finishSalesTransaction($request);
        return $add; 

    }
    public function getSalesbyID(Request $request){
      
                $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->getSalesbyID($request);
        return $add; 

    }
    public function getSalesDetailbyID(Request $request){
      
                $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->getSalesDetailbyID($request);
        return $add; 

    }
    public function getSalesbyDateUser(Request $request){
      
                $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->getSalesbyDateUser($request);
        return $add; 

    }
    public function getSalesbyPeriode(Request $request){
      
                $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->getSalesbyPeriode($request);
        return $add; 

    }
    public function getConsumableChargedPeriode(Request $request){
      
        $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                    $aDeliveryOrderRepository,
                    $aBarangRepository,
                    $asupplierRepository,
                    $sStokRepository,
                    $aHnaRepository,
                    $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

    $add =  $userService->getConsumableChargedPeriode($request);
    return $add; 

    }
    public function addSalesDetailv2(Request $request){
      
        $trsResepRepository = new aTrsResepRepositoryImpl(); 
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl();
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl();
        $sStokRepository = new aStokRepositoryImpl();    
        $aHnaRepository = new aHnaRepositoryImpl();
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl(); 
        $aSalesRepository = new aSalesRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $billingRepository = new bBillingRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository );

        $add =  $userService->addSalesDetailV2($request);
        return $add; 

    }
}
