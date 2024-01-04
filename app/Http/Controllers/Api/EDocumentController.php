<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repository\bEdocumentRepositoryImpl;
use App\Http\Service\bEdocumentService;
use Illuminate\Http\Request;

class EDocumentController extends Controller
{
    //
    public function verifygeneralconsen($uuid){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->verifygeneralconsen($uuid);
        return $user; 
    }
    public function generalconsen($uuid){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->generalconsen($uuid);
        return $user; 
    }
    public function akadijaroh($uuid){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->akadijaroh($uuid);
        return $user; 
    }
    public function tatatertib($uuid){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->tatatertib($uuid);
        return $user; 
    }
    public function hakdankewajiban($uuid){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->hakdankewajiban($uuid);
        return $user; 
    }
    public function perkiraanbiayaoperasi($uuid){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->perkiraanbiayaoperasi($uuid);
        return $user; 
    }
    public function perkiraanbiayanonoperasi($uuid){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->perkiraanbiayanonoperasi($uuid);
        return $user; 
    }
    public function getlaboratoriumdocregistrasi(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getlaboratoriumdocregistrasi($request);
        return $user; 
    }
    public function getRadiologidocregistrasi(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getRadiologidocregistrasi($request);
        return $user; 
    }
    public function getResumeMedisdocregistrasi(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getResumeMedisdocregistrasi($request);
        return $user; 
    }
    public function getResumeMedisbyId(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getResumeMedisbyId($request);
        return $user; 
    }
    public function insertOTP(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->insertOTP($request);
        return $user; 
    }
    public function verifyOTP(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->verifyOTP($request);
        return $user; 
    }
    //Tambahan 25-12-2023
    public function getPersetujuanTindakandocregistrasi(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getPersetujuanTindakandocregistrasi($request);
        return $user; 
    }
    public function getPersetujuanTindakanbyId(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getPersetujuanTindakanbyId($request);
        return $user; 
    }
    public function getSuketSakitdocregistrasi(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getSuketSakitdocregistrasi($request);
        return $user; 
    }
    public function getSuketSakitbyId(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getSuketSakitbyId($request);
        return $user; 
    }
    public function getSuketSehatdocregistrasi(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getSuketSehatdocregistrasi($request);
        return $user; 
    }
    public function getSuketSehatbyId(Request $request){
        $eDocuemnt = new bEdocumentRepositoryImpl();   
        $userService = new bEdocumentService($eDocuemnt);
        $user =  $userService->getSuketSehatbyId($request);
        return $user; 
    }
}
