<?php

namespace App\Http\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\UserRepositoryImpl;
use App\Http\Repository\aUnitRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\aSatuanRepositoryImpl;
use App\Http\Repository\aScheduleDoctorRepositoryImpl;

class aScheduleDoctorService extends Controller
{

    private $Repository;
    private $doctorRepository;

    public function __construct(aScheduleDoctorRepositoryImpl $Repository,aDoctorRepositoryImpl $doctorRepository)
    {
        $this->Repository = $Repository;
        $this->doctorRepository = $doctorRepository;
    }

    public function getScheduleDoctorbyUnitDay(Request $request)
    {   
        // $request->validate([
        //     "IdUnit" => "required",
        //     "Day" => "required" 
        // ]);

        // $rules = [
        //     'IdUnit' => 'required',
        //     'Day' => 'required',
        // ];
        // $customMessages = [
        //     'required' => ':attribute Masih Kosong.'
        // ];
        // $this->validate($request, $rules, $customMessages);

        $validatedData = $request->validate([
            'IdUnit' => 'required',
            'Day' => 'required',
        ],
        [
         'IdUnit.required'=> 'Your First Name is Required', // custom message
         'Day.required'=> 'Hari harus Diisi', // custom message 
        ]
     );

        try {   
            if ($request->Day === "Minggu") {
                $data = $this->Repository->getScheduleDoctorMinggu($request);
            } elseif ($request->Day === "Senin") {
                $data = $this->Repository->getScheduleDoctorSenin($request);
            } elseif ($request->Day === "Selasa") {
                $data = $this->Repository->getScheduleDoctorSelasa($request);
            } elseif ($request->Day === "Rabu") {
                $data = $this->Repository->getScheduleDoctorRabu($request);
            } elseif ($request->Day === "Kamis") {
                $data = $this->Repository->getScheduleDoctorKamis($request);
            } elseif ($request->Day === "Jumat") {
                $data = $this->Repository->getScheduleDoctorJumat($request);
            } elseif ($request->Day === "Sabtu") {
                $data = $this->Repository->getScheduleDoctorSabtu($request);
            }

            $count = $data->count();
            if ($count > 0) { 
                return $this->sendResponse($data, "Data Jadwal Dokter ditemukan.");
            } else {
                return $this->sendError("Data Jadwal Dokter not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
 
    public function getScheduleDoctorAll()
    {
        try {   
            $count = $this->Repository->getScheduleDoctorAll()->count();
            if ($count > 0) {
                $data = $this->Repository->getScheduleDoctorAll();
                return $this->sendResponse($data, "Data Unit Poliklinik ditemukan.");
            } else {
                return $this->sendError("Data Unit Poliklinik Not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getScheduleDoctorDetilbyId($request)
    {
        try {   
            $datadokter = [];
            $dataschedule = $this->Repository->getScheduleDoctorDetilbyId($request->IdDokter);
            $datadokter = $this->doctorRepository->getDoctorbyId($request->IdDokter)->first();
            $response = [
                'dokter' => $datadokter, 
                'schedule' => $dataschedule , 
            ];
            return $this->sendResponse($response, "Data Schedule ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getScheduleDoctorDetilNonBPJSbyId($request)
    {
        try {   
            $datadokter = [];
            $dataschedule = $this->Repository->getScheduleDoctorDetilNonBPJSbyId($request->IdDokter);
            $datadokter = $this->doctorRepository->getDoctorNonIGDbyId($request->IdDokter)->first();
            $response = [
                'dokter' => $datadokter, 
                'schedule' => $dataschedule , 
            ];
            return $this->sendResponse($response, "Data Schedule ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getScheduleDoctorbyIdDoctor($request)
    {
        $request->validate([
            "IdUnit" => "required",
            "Day" => "required" ,
            "IdDokter" => "required"
        ]);
        try {   
            if ($request->Day === "Minggu") {
                $data = $this->Repository->getScheduleDoctorMinggu($request);
            } elseif ($request->Day === "Senin") {
                $data = $this->Repository->getScheduleDoctorSenin($request);
            } elseif ($request->Day === "Selasa") {
                $data = $this->Repository->getScheduleDoctorSelasa($request);
            } elseif ($request->Day === "Rabu") {
                $data = $this->Repository->getScheduleDoctorRabu($request);
            } elseif ($request->Day === "Kamis") {
                $data = $this->Repository->getScheduleDoctorKamis($request);
            } elseif ($request->Day === "Jumat") {
                $data = $this->Repository->getScheduleDoctorJumat($request);
            } elseif ($request->Day === "Sabtu") {
                $data = $this->Repository->getScheduleDoctorSabtu($request);
            }

            $count = $data->count();
            if ($count > 0) { 
                return $this->sendResponse($data, "Data Jadwal Dokter ditemukan.");
            } else {
                return $this->sendError("Data Jadwal Dokter Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getScheduleSelectedDay(Request $request)
    {   

        try {   
            if ($request->Day === "Minggu") {
                $data = $this->Repository->getScheduleDoctorForTRSMinggu($request->IdDokter,$request->IdUnit,$request->jampraktek,$request->Group_Jadwal);
            } elseif ($request->Day === "Senin") {
                $data = $this->Repository->getScheduleDoctorForTRSSenin($request->IdDokter,$request->IdUnit,$request->jampraktek,$request->Group_Jadwal);
            } elseif ($request->Day === "Selasa") {
                $data = $this->Repository->getScheduleDoctorForTRSSelasa($request->IdDokter,$request->IdUnit,$request->jampraktek,$request->Group_Jadwal);
            } elseif ($request->Day === "Rabu") {
                $data = $this->Repository->getScheduleDoctorForTRSRabu($request->IdDokter,$request->IdUnit,$request->jampraktek,$request->Group_Jadwal);
            } elseif ($request->Day === "Kamis") {
                $data = $this->Repository->getScheduleDoctorForTRSKamis($request->IdDokter,$request->IdUnit,$request->jampraktek,$request->Group_Jadwal);
            } elseif ($request->Day === "Jumat") {
                $data = $this->Repository->getScheduleDoctorForTRSJumat($request->IdDokter,$request->IdUnit,$request->jampraktek,$request->Group_Jadwal);
            } elseif ($request->Day === "Sabtu") {
                $data = $this->Repository->getScheduleDoctorForTRSSabtu($request->IdDokter,$request->IdUnit,$request->jampraktek,$request->Group_Jadwal);
            }

            $count = $data->count();
            if ($count > 0) { 
                return $this->sendResponse($data, "Data Jadwal Dokter ditemukan.");
            } else {
                return $this->sendError("Data Jadwal Dokter not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getScheduleSelectedDayGroupByDoctor(Request $request)
    {   

        try {   
            if ($request->Day === "Minggu") {
                $data = $this->Repository->getScheduleGroubyDoctorForTRSMinggu($request->IdUnit,$request->Group_Jadwal);
            } elseif ($request->Day === "Senin") {
                $data = $this->Repository->getScheduleGroubyDoctorForTRSSenin($request->IdUnit,$request->Group_Jadwal);
            } elseif ($request->Day === "Selasa") {
                $data = $this->Repository->getScheduleGroubyDoctorForTRSSelasa($request->IdUnit,$request->Group_Jadwal);
            } elseif ($request->Day === "Rabu") {
                $data = $this->Repository->getScheduleGroubyDoctorForTRSRabu($request->IdUnit,$request->Group_Jadwal);
            } elseif ($request->Day === "Kamis") {
                $data = $this->Repository->getScheduleGroubyDoctorForTRSKamis($request->IdUnit,$request->Group_Jadwal);
            } elseif ($request->Day === "Jumat") {
                $data = $this->Repository->getScheduleGroubyDoctorForTRSJumat($request->IdUnit,$request->Group_Jadwal);
            } elseif ($request->Day === "Sabtu") {
                $data = $this->Repository->getScheduleGroubyDoctorForTRSSabtu($request->IdUnit,$request->Group_Jadwal);
            }

            $count = $data->count();
            if ($count > 0) { 
                return $this->sendResponse($data, "Data Jadwal Dokter ditemukan.");
            } else {
                return $this->sendError("Data Jadwal Dokter not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getScheduleDoctorbyIdJadwalDoctor($request)
    {
        try {   
            
            $dataschedule = $this->Repository->getScheduleDoctorbyIdJadwalDoctor($request->IdJadwalDokter);
            return $this->sendResponse($dataschedule, "Data Schedule ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getScheduleDoctorbyIdDoctorUnit($request)
    {
        try {   
            $datadokter = [];
            $dataschedule = $this->Repository->getScheduleDoctorbyIdDoctorUnit($request);
            $datadokter = $this->doctorRepository->getDoctorNonIGDbyIdDokterUnit($request)->first();
            $response = [
                'dokter' => $datadokter, 
                'schedule' => $dataschedule , 
            ];
            return $this->sendResponse($response, "Data Schedule ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
}
