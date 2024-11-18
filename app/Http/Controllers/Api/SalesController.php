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
use App\Http\Repository\aReturJualRepositoryImpl;
use App\Http\Repository\bAntrianFarmasiRepositoryImpl;

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

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
        $returJualRepository = new aReturJualRepositoryImpl();
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

        $add =  $userService->addSalesDetailV2($request);
        return $add; 

    }
    public function getSalesDetailbyNoReg(Request $request){
      
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
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

        $add =  $userService->getSalesDetailbyNoReg($request);
        return $add; 

        }

        public function getSalesbyPeriodeResep(Request $request){
      
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
            $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

    $add =  $userService->getSalesbyPeriodeResep($request);
    return $add; 

}

        public function getSalesbyIDandNoResep(Request $request){
            
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
            $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

        $add =  $userService->getSalesbyIDandNoResep($request);
        return $add; 

        }

        public function getSalesDetailbyIDandNoResep(Request $request){
            
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
            $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

        $add =  $userService->getSalesDetailbyIDandNoResep($request);
        return $add; 

        }

            public function getSalesbyPeriodeTanpaResep(Request $request){
        
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
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

        $add =  $userService->getSalesbyPeriodeTanpaResep($request);
        return $add; 

    }

    public function voidSalesTebus(Request $request){
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
        $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
        $userService = new aSalesService($trsResepRepository,
                            $aDeliveryOrderRepository,
                            $aBarangRepository,
                            $asupplierRepository,
                            $sStokRepository,
                            $aHnaRepository,
                            $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);

        $add =  $userService->voidSalesTebus($request);
        return $add; 

        }

        //tambahan 30-10-2024 code:30102024 dan antrian
        public function getSalesbyPeriodeResepRajal(Request $request){
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
            $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
            $userService = new aSalesService($trsResepRepository,
                                $aDeliveryOrderRepository,
                                $aBarangRepository,
                                $asupplierRepository,
                                $sStokRepository,
                                $aHnaRepository,
                                $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);
    
            $add =  $userService->getSalesbyPeriodeResepRajal($request);
            return $add; 
    
            }

            
        //tambahan 05-11-2024 code:05112024 dan antrian
        public function getSalesbyPeriodeResepRanap(Request $request){
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
            $aAntrianFarmasiRepository = new bAntrianFarmasiRepositoryImpl();
            $userService = new aSalesService($trsResepRepository,
                                $aDeliveryOrderRepository,
                                $aBarangRepository,
                                $asupplierRepository,
                                $sStokRepository,
                                $aHnaRepository,
                                $aMasterUnitRepository,$aSalesRepository ,$visitRepository,$billingRepository,$returJualRepository,$aAntrianFarmasiRepository);
    
            $add =  $userService->getSalesbyPeriodeResepRanap($request);
            return $add; 
    
            }
}
