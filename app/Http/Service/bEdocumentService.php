<?php

namespace App\Http\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\bEdocumentRepositoryImpl;
use Illuminate\Support\Facades\Validator; 
class bEdocumentService extends Controller
{

    private $bEducationRepository;

    public function __construct(bEdocumentRepositoryImpl $bEducationRepository)
    {
        $this->bEducationRepository = $bEducationRepository;
    }

    public function verifygeneralconsen($uuid)
    {
       
        try {   
            // validator 
            $data = $this->bEducationRepository->verify($uuid);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }

    public function generalconsen($uuid)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->generalconsen($uuid);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function akadijaroh($uuid)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->akadijaroh($uuid);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function tatatertib($uuid)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->tatatertib($uuid);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function hakdankewajiban($uuid)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->hakdankewajiban($uuid);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function perkiraanbiayaoperasi($uuid)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->perkiraanbiayaoperasi($uuid);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function perkiraanbiayanonoperasi($uuid)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->perkiraanbiayanonoperasi($uuid);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }


    public function getlaboratoriumdocregistrasi($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getlaboratoriumdocregistrasi($request->NoRegistrasi);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getRadiologidocregistrasi($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getRadiologidocregistrasi($request->NoRegistrasi);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getResumeMedisdocregistrasi($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getResumeMedisdocregistrasi($request->NoRegistrasi);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getResumeMedisbyId($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getResumeMedisbyId($request->IdTrs);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function insertOTP($request)
    {
        try {   
            DB::connection('Syslog')->beginTransaction();
            // validator 
            if ($request->nohp == "") {    
                return $this->sendError("No. Handphone Kosong.", []);
            }
            $this->bEducationRepository->updateOTPExpired($request);
            $this->bEducationRepository->insertOTP($request);
            DB::connection('Syslog')->commit();
            return $this->sendResponse([] ,"Otp Berhasil Disimpan.");  

        }catch (Exception $e) { 
            DB::connection('Syslog')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError('Data Gagal !', $e->getMessage());
        }
    }
    public function verifyOTP($request)
    {
        try {   
            DB::connection('Syslog')->beginTransaction();
            // validator  
            
            if ($request->otp == "") {    
                return $this->sendError("Kode OTP Kosong.", []);
            }
            if ($request->nohp == "") {    
                return $this->sendError("No. Handphone Kosong.", []);
            }
            if ($request->jenisotp == "") {    
                return $this->sendError("Jenis OTP Kosong.", []);
            }

            $data = $this->bEducationRepository->getOTPActive($request);
        
            if ($data->count() < 1) { 
                return $this->sendError( "Data OTP Invalid.",[]);
            }  

            $this->bEducationRepository->updateOTPVerify($request); 
            $this->bEducationRepository->updateOTPVerifiedResumeMedis($request);
            DB::connection('Syslog')->commit();
            return $this->sendResponse([] ,"Otp Verifikasi Berhasil.");  

        }catch (Exception $e) { 
            DB::connection('Syslog')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError('Data Gagal !', $e->getMessage());
        }
    }
    //Tambahan 25-12-2023
    public function getPersetujuanTindakandocregistrasi($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getPersetujuanTindakandocregistrasi($request->NoRegistrasi);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getPersetujuanTindakanbyId($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getPersetujuanTindakanbyId($request->IdTrs);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getSuketSakitdocregistrasi($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getSuketSakitdocregistrasi($request->NoRegistrasi);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getSuketSakitbyId($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getSuketSakitbyId($request->IdTrs);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getSuketSehatdocregistrasi($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getSuketSehatdocregistrasi($request->NoRegistrasi);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getSuketSehatbyId($request)
    {
        try {   
            // validator 
            $data = $this->bEducationRepository->getSuketSehatbyId($request->IdTrs);
            $count = $data->count(); 
            if ($count > 0) {
                $dataFix = $data->first();
                return $this->sendResponse($dataFix, "Data Document ditemukan.");
            } else {
                return $this->sendError("Data Document tidak ditemukan.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
}