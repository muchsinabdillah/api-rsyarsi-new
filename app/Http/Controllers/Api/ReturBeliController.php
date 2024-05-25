<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Http\Service\aReturBeliService;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aReturBeliRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;

class ReturBeliController extends Controller
{
    //a
    public function addReturBeliHeader(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->addReturBeliHeader($request);
        return $execute;
    }
    public function addReturBeliFinish(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->addReturBeliFinish($request);
        return $execute;
    }
    public function voidReturBeli(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->voidReturBeli($request);
        return $execute;
    }
    public function voidReturBeliDetailbyItem(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->voidReturBeliDetailbyItem($request);
        return $execute;
    }
    public function getReturBelibyID(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->getReturBelibyID($request);
        return $execute;
    }
    public function getReturBeliDetailbyID(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->getReturBeliDetailbyID($request);
        return $execute;
    }
    public function getReturBelibyDateUser(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->getReturBelibyDateUser($request);
        return $execute;
    }
    public function getReturBelibyPeriode(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $asupplierRepository = new aSupplierRepositoryImpl(); 
        $aStokRepository = new aStokRepositoryImpl();
        $aDeliveryOrderRepository = new aDeliveryOrderRepositoryImpl(); 
        $aMasterUnitRepository = new aMasterUnitRepositoryImpl();
        $ahnaRepository =  new aHnaRepositoryImpl(); 
        $returbeliRepository =  new aReturBeliRepositoryImpl(); 
        $aReturBeliService = new aReturBeliService(  
            $aBarangRepository,
            $asupplierRepository,
            $aStokRepository,
            $aDeliveryOrderRepository,
            $aMasterUnitRepository, 
            $ahnaRepository,
            $returbeliRepository  
        );
        $execute =  $aReturBeliService->getReturBelibyPeriode($request);
        return $execute;
    }

}
