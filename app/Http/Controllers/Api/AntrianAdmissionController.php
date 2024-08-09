<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repository\UserRepositoryImpl;
use App\Http\Service\AntrianAdmissionService;
use App\Http\Repository\bAntrianAdmissionRepositoryImpl;
use App\Http\Repository\cMasterDataAntrianRepositoryImpl;

class AntrianAdmissionController extends Controller
{
    //
    public function CreateAntrianAdmission(Request $request)
    {
        $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->CreateAntrian($request);
    }
    public function CreateAntrianAdmissionAPM(Request $request)
    {
        $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->CreateAntrianAdmissionAPM($request);
    }
    public function ListAntrianAdmission(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->ListAntrianAdmission($request);
    }
    public function PanggilAntrian(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->PanggilAntrian($request);
    }
    public function HoldAntrian(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->HoldAntrian($request);
    }
    public function ProccesedAntrian(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->ProccesedAntrian($request);
    }
    public function ClosedAntrian(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->ClosedAntrian($request);
    } 

    public function ViewbyIdTrsAntrianAdmission(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->ViewbyIdTrsAntrianAdmission($request);
    } 
    public function ViewbyDateTrsAntrianAdmission(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->ViewbyDateTrsAntrianAdmission($request);
    } 
    public function ViewbyDateTrsJaminanAntrianAdmission(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->ViewbyDateTrsJaminanAntrianAdmission($request);
    } 
    public function ViewbyFloorTrsAntrianAdmission(Request $request)
    {
       $masterAntrian = new cMasterDataAntrianRepositoryImpl();
        $trsAntrianAdmission = new bAntrianAdmissionRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $antraiAdmisioService = new AntrianAdmissionService($masterAntrian,$trsAntrianAdmission,$userLoginRepository);
        return  $antraiAdmisioService->ViewbyFloorTrsAntrianAdmission($request);
    } 
}
