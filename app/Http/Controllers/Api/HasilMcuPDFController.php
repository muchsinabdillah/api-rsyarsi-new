<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\bHasilMCUService;
use App\Http\Repository\bHasilMCURepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;

class HasilMcuPDFController extends Controller
{
  
    public function uploaPdfMedicalCheckupbyKodeJenis(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->uploaPdfMedicalCheckupbyKodeJenis($request);
        return $user; 
    }
    
    public function uploaPdfHasilMCUFinish(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->uploaPdfHasilMCUFinish($request);
        return $user; 
    }
    public function hasilMCU(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->hasilMCU($request);
        return $user; 
    }
    public function listDocumentMCU(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->listDocumentMCU($request);
        return $user; 
    }
    public function listReportPDFMCU(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->listReportPDFMCU($request);
        return $user; 
    }
    public function hasilMCUTreadmill(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->hasilMCUTreadmill($request);
        return $user; 
    }
    public function hasilMCUJiwa(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->hasilMCUJiwa($request);
        return $user; 
    }
    public function hasilMCUBebasNarkoba(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->hasilMCUBebasNarkoba($request);
        return $user; 
    }
    public function getRekapSDSbyPeriode(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->getRekapSDSbyPeriode($request);
        return $user; 
    }
    public function getRekapMCU(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->getRekapMCU($request);
        return $user; 
    }
    public function uploaPdfMedicalCheckupGenerate(Request $request){
        $hasilmcu = new bHasilMCURepositoryImpl();  
        $visit = new bVisitRepositoryImpl();  
        $userService = new bHasilMCUService($hasilmcu,$visit);
        $user =  $userService->uploaPdfMedicalCheckupGenerate($request);
        return $user; 
    }
}


