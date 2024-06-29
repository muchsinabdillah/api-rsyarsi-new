<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\aScheduleDoctorService;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\aScheduleDoctorRepositoryImpl;

class ScheduleDoctorController extends Controller
{
    public function getScheduleDoctorbyUnitDay(Request $request){
         $Repository = new aScheduleDoctorRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $Service = new aScheduleDoctorService($Repository,$doctorRepository);
        $getScheduleDoctorbyUnitDay =  $Service->getScheduleDoctorbyUnitDay($request);
        return $getScheduleDoctorbyUnitDay;
    }
    public function getScheduleDoctorAll(){
        $Repository = new aScheduleDoctorRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $Service = new aScheduleDoctorService($Repository,$doctorRepository);
        $getScheduleDoctorbyUnitDay =  $Service->getScheduleDoctorAll();
        return $getScheduleDoctorbyUnitDay;
    }
    public function getScheduleDoctorbyIdDoctor(Request $request){
        $Repository = new aScheduleDoctorRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $Service = new aScheduleDoctorService($Repository,$doctorRepository);
        $getScheduleDoctorbyUnitDay =  $Service->getScheduleDoctorbyIdDoctor($request);
        return $getScheduleDoctorbyUnitDay;
    }
    public function getScheduleDoctorDetilbyId(Request $request){
         $Repository = new aScheduleDoctorRepositoryImpl();
        $doctorRepository = new aDoctorRepositoryImpl();
        $Service = new aScheduleDoctorService($Repository,$doctorRepository);
        $getScheduleDoctorbyUnitDay =  $Service->getScheduleDoctorDetilbyId($request);
        return $getScheduleDoctorbyUnitDay;
    }
    public function getScheduleDoctorDetilNonBPJSbyId(Request $request){
        $Repository = new aScheduleDoctorRepositoryImpl();
       $doctorRepository = new aDoctorRepositoryImpl();
       $Service = new aScheduleDoctorService($Repository,$doctorRepository);
       $getScheduleDoctorbyUnitDay =  $Service->getScheduleDoctorDetilNonBPJSbyId($request);
       return $getScheduleDoctorbyUnitDay;
   }
    public function getScheduleSelectedDay(Request $request){
        $Repository = new aScheduleDoctorRepositoryImpl();
       $doctorRepository = new aDoctorRepositoryImpl();
       $doctorRepository = new aDoctorRepositoryImpl();
       $Service = new aScheduleDoctorService($Repository,$doctorRepository);
       $getScheduleDoctorbyUnitDay =  $Service->getScheduleSelectedDay($request);
       return $getScheduleDoctorbyUnitDay;
    }
    public function getScheduleSelectedDayGroupByDoctor(Request $request){
        $Repository = new aScheduleDoctorRepositoryImpl();
       $doctorRepository = new aDoctorRepositoryImpl();
       $doctorRepository = new aDoctorRepositoryImpl();
       $Service = new aScheduleDoctorService($Repository,$doctorRepository);
       $getScheduleDoctorbyUnitDay =  $Service->getScheduleSelectedDayGroupByDoctor($request);
       return $getScheduleDoctorbyUnitDay;
    }
    public function getScheduleDoctorbyIdJadwalDoctor(Request $request){
        $Repository = new aScheduleDoctorRepositoryImpl();
       $doctorRepository = new aDoctorRepositoryImpl();
       $Service = new aScheduleDoctorService($Repository,$doctorRepository);
       $getScheduleDoctorbyUnitDay =  $Service->getScheduleDoctorbyIdJadwalDoctor($request);
       return $getScheduleDoctorbyUnitDay;
   }
   public function getScheduleDoctorbyIdDoctorUnit(Request $request){
    $Repository = new aScheduleDoctorRepositoryImpl();
   $doctorRepository = new aDoctorRepositoryImpl();
   $Service = new aScheduleDoctorService($Repository,$doctorRepository);
   $getScheduleDoctorbyUnitDay =  $Service->getScheduleDoctorbyIdDoctorUnit($request);
   return $getScheduleDoctorbyUnitDay;
}
}
