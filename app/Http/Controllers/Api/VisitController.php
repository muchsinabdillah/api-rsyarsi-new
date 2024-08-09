<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Service\bVisitService;
use App\Http\Controllers\Controller;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\bAntrianRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\bAppointmentRepositoryImpl;
use App\Http\Repository\bKamarOperasiRepositoryImpl;
use App\Http\Repository\bMedicalRecordRepositoryImpl;
use App\Http\Repository\aScheduleDoctorRepositoryImpl;

class VisitController extends Controller
{
    //
    public function viewByNoregistrasi(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->viewByNoregistrasi($request);
        return $user; 
    }
    public function updateNoSepbyNoRegistrasi(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->updateNoSepbyNoRegistrasi($request);
        return $user; 
    }
    public function viewByAppointmentNumber(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->viewByAppointmentNumber($request);
        return $user; 
    }
    public function getRegistrationRajalbyMedreActive(Request $request){
               $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->getRegistrationRajalbyMedreActive($request);
        return $user; 
    }
    public function getRegistrationRajalbyMedreHistory(Request $request){
               $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->getRegistrationRajalbyMedreHistory($request);
        return $user; 
    }

    public function getRegistrationRajalbyDoctorActive(Request $request){
               $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->getRegistrationRajalbyDoctorActive($request);
        return $user; 
    }
    public function getRegistrationRajalbyDoctorHistory(Request $request){
               $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->getRegistrationRajalbyDoctorHistory($request);
        return $user; 
    }
    public function createRegistrasiOnsite(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->createRegistrasiOnsite($request);
        return $user; 
    }
    public function viewByNoBooking(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->viewByNoBooking($request);
        return $user; 
    }
    public function insertSEP(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->insertSEP($request);
        return $user; 
    }
    public function viewsep(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->viewsep($request);
        return $user; 
    }
    public function viewsepBefore(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->viewsepBefore($request);
        return $user; 
    }
    public function addTaskBPJS(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->addTaskBPJS($request);
        return $user; 
    }
    public function getRegistrationMCUbyDate(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->getRegistrationMCUbyDate($request);
        return $user; 
    }
    //coass
    public function getRegistrationRajalActiveCoas(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->getRegistrationRajalActiveCoas($request);
        return $user; 
    }
        public function getRegistrationRajalHistoryCoas(Request $request){
                $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->getRegistrationRajalHistoryCoas($request);
        return $user; 
    }
    public function viewByNoregistrasiRanap(Request $request){
        $userRepository = new bKamarOperasiRepositoryImpl();
        $medrecRepository = new bMedicalRecordRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $unitRepository = new aMasterUnitRepositoryImpl();
        $appointmenRepository = new bAppointmentRepositoryImpl();
        $scheduleRepository = new aScheduleDoctorRepositoryImpl();
        $antrianRepository = new bAntrianRepositoryImpl();
        $visitRepository = new bVisitRepositoryImpl();
        $userService = new bVisitService($userRepository,$medrecRepository,
        $doctorRepository,$unitRepository, $appointmenRepository,$scheduleRepository,
        $antrianRepository,$visitRepository);
        $user =  $userService->viewByNoregistrasiRanap($request);
        return $user; 
    }
}
