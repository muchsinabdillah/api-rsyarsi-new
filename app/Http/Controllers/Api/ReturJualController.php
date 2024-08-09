<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\aReturJualService;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aSalesRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\bBillingRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aTrsResepRepositoryImpl;
use App\Http\Repository\aReturJualRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;

class ReturJualController extends Controller
{
    //
    public function addReturJualHeader(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->addReturJualHeader($request);
        return $add; 

    }
    public function addReturJualFinish(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->addReturJualFinish($request);
        return $add; 

    }
    public function voidReturJualDetailbyItem(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->voidReturJualDetailbyItem($request);
        return $add; 

    }
    public function voidReturJual(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->voidReturJual($request);
        return $add; 

    }
    public function getReturJualbyID(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->getReturJualbyID($request);
        return $add; 

    }
    public function getReturJualDetailbyID(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->getReturJualDetailbyID($request);
        return $add; 

    }

    public function getReturJualbyDateUser(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->getReturJualbyDateUser($request);
        return $add; 

    }

    public function getReturJualbyPeriode(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->getReturJualbyPeriode($request);
        return $add; 

    }
    public function addReturJualHeaderbyNoReg(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->addReturJualHeaderbyNoReg($request);
        return $add; 

    }

    public function addReturJualFinishbyReg(Request $request)
    {
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
        $returJualRepository = new aReturJualRepositoryImpl();
        $userService = new aReturJualService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository );

        $add =  $userService->addReturJualFinishbyReg($request);
        return $add; 

    }
}
