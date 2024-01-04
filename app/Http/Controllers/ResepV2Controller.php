<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Service\bTrsResepService;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\aTrsResepRepositoryImpl;

class ResepV2Controller extends Controller
{
    //
    public function viewOrderResepbyDatePeriode(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->viewOrderReseV2pbyDatePeriode($request);
        return $user; 
    }

    public function viewOrderResepbyTrs(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->viewOrderResepbyTrsV2($request);
        return $user; 
    }

    public function viewOrderResepbyOrderIDV2(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->viewOrderResepbyOrderIDV2($request);
        return $user; 
    }

    public function viewOrderResepDetailbyOrderIDV2(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->viewOrderResepDetailbyOrderIDV2($request);
        return $user; 
    }

    public function editSignaTerjemahanbyID(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->editSignaTerjemahanbyID($request);
        return $user; 
    }

    public function viewprintLabelbyID(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->viewprintLabelbyID($request);
        return $user; 
    }

    public function getPrinterLabel(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->getPrinterLabel($request);
        return $user; 
    }

    public function editReviewbyIDResep(Request $request){
        $visitRepository = new bVisitRepositoryImpl(); 
        $trsResep = new aTrsResepRepositoryImpl(); 
        $doctorRepository = new aDoctorRepositoryImpl(); 
        $hnaRepository = new aHnaRepositoryImpl();
        $userService = new bTrsResepService($visitRepository,$trsResep,$doctorRepository,$hnaRepository);
        $user =  $userService->editReviewbyIDResep($request);
        return $user; 
    }
   
}
