<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\bAssesmentRajalService;
use App\Http\Service\bAppointmenNonBPJSService;
use App\Http\Repository\bAssesmentRajalRepositoryImpl;
use App\Http\Repository\bRegistrationRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;

class AssesmentController extends Controller
{
    //
    public function CreateAssesmentRajal(Request $request){
        $assesmentRajal = new bAssesmentRajalRepositoryImpl();
        $registrationRajal = new bVisitRepositoryImpl();
        $userService = new bAssesmentRajalService($assesmentRajal, $registrationRajal);
        $user =  $userService->CreateAssesmentRajal($request);
        return $user; 
    }
    public function UpdateAssesmentRajal(Request $request){
        $assesmentRajal = new bAssesmentRajalRepositoryImpl();
        $registrationRajal = new bVisitRepositoryImpl();
        $userService = new bAssesmentRajalService($assesmentRajal, $registrationRajal);
        $user =  $userService->UpdateAssesmentRajal($request);
        return $user; 
    }
    public function viewAssesmentRajal(Request $request){
        $assesmentRajal = new bAssesmentRajalRepositoryImpl();
        $registrationRajal = new bVisitRepositoryImpl();
        $userService = new bAssesmentRajalService($assesmentRajal, $registrationRajal);
        $user =  $userService->viewAssesmentRajal($request);
        return $user; 
    }
    public function viewAssesmentRajalPerawat(Request $request){
        $assesmentRajal = new bAssesmentRajalRepositoryImpl();
        $registrationRajal = new bVisitRepositoryImpl();
        $userService = new bAssesmentRajalService($assesmentRajal, $registrationRajal);
        $user =  $userService->viewAssesmentRajalPerawat($request);
        return $user; 
    }
    public function ViewCppt(Request $request){
        $assesmentRajal = new bAssesmentRajalRepositoryImpl();
        $registrationRajal = new bVisitRepositoryImpl();
        $userService = new bAssesmentRajalService($assesmentRajal, $registrationRajal);
        $user =  $userService->ViewCppt($request);
        return $user; 
    }
    public function ViewCpptPeriode(Request $request){
        $assesmentRajal = new bAssesmentRajalRepositoryImpl();
        $registrationRajal = new bVisitRepositoryImpl();
        $userService = new bAssesmentRajalService($assesmentRajal, $registrationRajal);
        $user =  $userService->ViewCpptPeriode($request);
        return $user; 
    }
    public function dataoperasiharian(Request $request){
        $assesmentRajal = new bAssesmentRajalRepositoryImpl();
        $registrationRajal = new bVisitRepositoryImpl();
        $userService = new bAssesmentRajalService($assesmentRajal, $registrationRajal);
        $user =  $userService->dataoperasiharian($request);
        return $user; 
    }
}
