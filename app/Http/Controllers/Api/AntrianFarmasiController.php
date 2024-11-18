<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\bAntrianRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\bAppointmentRepositoryImpl;
use App\Http\Repository\bKamarOperasiRepositoryImpl;
use App\Http\Repository\bMedicalRecordRepositoryImpl;
use App\Http\Repository\aScheduleDoctorRepositoryImpl;
use App\Http\Repository\bAntrianFarmasiRepositoryImpl;
use App\Http\Service\AntrianFarmasiService;
use App\Http\Repository\UserRepositoryImpl;

class AntrianFarmasiController extends Controller
{
    //a
    public function CreateAntrianFarmasi(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->CreateAntrian($request);
        return $user;
    }
    public function CreateAntrianFarmasiNew(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->CreateAntrianFarmasiNew($request);
        return $user;
    }
    public function UpdateAntrianFarmasi(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->UpdateAntrianFarmasi($request);
        return $user;
    }
    public function ListAntrianFarmasi(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->ListAntrianFarmasi($request);
        return $user;
    }
    public function ListHistoryAntrianFarmasi(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->ListHistoryAntrianFarmasi($request);
        return $user;
    }
    public function ListDepoFarmasi(){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->ListDepoFarmasi();
        return $user;
    }
    public function UpdateDataVerifikasiAmbilResep(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->UpdateDataVerifikasiAmbilResep($request);
        return $user;
    }
    public function UpdateDataVerifikasi(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->UpdateDataVerifikasi($request);
        return $user;
    }
    public function ListAntrianFinish(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->ListAntrianFinish($request);
        return $user;
    }
    public function SendVerificationNumber(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->SendVerificationNumber($request);
        return $user;
    }
    public function RuningText(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->RuningText($request);
        return $user;
    }
    public function ViewResepMedrecbyDate(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->ViewResepMedrecbyDate($request);
        return $user;
    }
    public function ViewHistoryResepMedrecbyNoResep(Request $request){
         $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
        $userLoginRepository = new UserRepositoryImpl();
        $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                        $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
        $user =  $userService->ViewHistoryResepMedrecbyNoResep($request);
        return $user;
    }
    public function ListAntrianTV(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
       $medrecRepository = new bMedicalRecordRepositoryImpl();
       $doctorRepository = new aDoctorRepositoryImpl();
       $unitRepository = new aMasterUnitRepositoryImpl();
       $appointmenRepository = new bAppointmentRepositoryImpl();
       $scheduleRepository = new aScheduleDoctorRepositoryImpl();
       $antrianRepository = new bAntrianRepositoryImpl();
       $visitRepository = new bVisitRepositoryImpl();
       $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
       $userLoginRepository = new UserRepositoryImpl();
       $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                       $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                       $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
       $user =  $userService->ListAntrianTV($request);
       return $user;
   }
   
   public function UpdateDataVerifikasiV2(Request $request){
    $userRepository = new bKamarOperasiRepositoryImpl();
   $medrecRepository = new bMedicalRecordRepositoryImpl();
   $doctorRepository = new aDoctorRepositoryImpl();
   $unitRepository = new aMasterUnitRepositoryImpl();
   $appointmenRepository = new bAppointmentRepositoryImpl();
   $scheduleRepository = new aScheduleDoctorRepositoryImpl();
   $antrianRepository = new bAntrianRepositoryImpl();
   $visitRepository = new bVisitRepositoryImpl();
   $antrianFarmasi = new bAntrianFarmasiRepositoryImpl();
   $userLoginRepository = new UserRepositoryImpl();
   $userService = new AntrianFarmasiService($userRepository,$medrecRepository,
                   $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
                   $antrianRepository,$visitRepository,$antrianFarmasi,$userLoginRepository);
   $user =  $userService->UpdateDataVerifikasiV2($request);
   return $user;
}
}
