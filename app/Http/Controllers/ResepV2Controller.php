<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Service\bTrsResepService;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\aTrsResepRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aSalesRepositoryImpl;

class ResepV2Controller extends Controller
{
    //
    public function viewOrderResepbyDatePeriode(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewOrderReseV2pbyDatePeriode($request);
        return $user; 
    }

    public function viewOrderResepbyTrs(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewOrderResepbyTrsV2($request);
        return $user; 
    }

    public function viewOrderResepbyOrderIDV2(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewOrderResepbyOrderIDV2($request);
        return $user; 
    }

    public function viewOrderResepDetailbyOrderIDV2(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewOrderResepDetailbyOrderIDV2($request);
        return $user; 
    }

    public function editSignaTerjemahanbyID(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->editSignaTerjemahanbyID($request);
        return $user; 
    }

    public function viewprintLabelbyID(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewprintLabelbyID($request);
        return $user; 
    }

    public function getPrinterLabel(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->getPrinterLabel($request);
        return $user; 
    }

    public function editReviewbyIDResep(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->editReviewbyIDResep($request);
        return $user; 
    }

    public function addTebusResep(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->addTebusResep($request);
        return $user; 
    }

    public function viewOrderResepbyDatePeriodeTebus(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewOrderResepbyDatePeriodeTebus($request);
        return $user; 
    }
    
    //tambahan 05-11-2024 code:05112024
    public function viewOrderResepbyDatePeriodeRajal(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewOrderResepbyDatePeriodeRajal($request);
        return $user; 
    }
    public function viewOrderResepbyDatePeriodeRanap(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $aSalesRepository = new aSalesRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository,$unitRepository,$aSalesRepository);
        $user =  $userService->viewOrderResepbyDatePeriodeRanap($request);
        return $user; 
    }
   
}
