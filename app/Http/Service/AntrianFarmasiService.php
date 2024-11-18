<?php

namespace App\Http\Service;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\VerificationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\ApiExternalConsumeTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\UserRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\aPabrikRepositoryImpl;
use App\Http\Repository\bAntrianRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\bAppointmentRepositoryImpl;
use App\Http\Repository\bKamarOperasiRepositoryImpl;
use App\Http\Repository\bMedicalRecordRepositoryImpl;
use App\Http\Repository\aScheduleDoctorRepositoryImpl;
use App\Http\Repository\bAntrianFarmasiRepositoryImpl;

date_default_timezone_set("Asia/Jakarta");
class AntrianFarmasiService extends Controller
{
    use VerificationTrait;
    use ApiExternalConsumeTrait;
    private $kamaroperasiRepository;
    private $medrecRepository;
    private $doctorRepository;
    private $unitRepository;
    private $appointmenRepository;
    private $scheduleRepository;
    private $antrianRepository;
    private $visitRepository;
    private $aAntrianFarmasiRepository;
    private $userLoginRepository;
    public function __construct(
        bKamarOperasiRepositoryImpl $kamaroperasiRepository,
        bMedicalRecordRepositoryImpl $medrecRepository,
        aDoctorRepositoryImpl $doctorRepository,
        aMasterUnitRepositoryImpl $unitRepository,
        bAppointmentRepositoryImpl $appointmenRepository,
        aScheduleDoctorRepositoryImpl $scheduleRepository,
        bAntrianRepositoryImpl $antrianRepository,
        bVisitRepositoryImpl $visitRepository,
        bAntrianFarmasiRepositoryImpl $aAntrianFarmasiRepository,
        UserRepositoryImpl $userLoginRepository
    )
    {
        $this->kamaroperasiRepository = $kamaroperasiRepository;
        $this->medrecRepository = $medrecRepository;
        $this->doctorRepository = $doctorRepository;
        $this->unitRepository = $unitRepository;
        $this->appointmenRepository = $appointmenRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->antrianRepository = $antrianRepository;
        $this->visitRepository = $visitRepository;
        $this->aAntrianFarmasiRepository = $aAntrianFarmasiRepository;
        $this->userLoginRepository = $userLoginRepository;
    }

    public function CreateAntrian(Request $request)
    {
        if ($request->NoRegistrasi == "") {
            return $this->sendError("Antrian Atas No. Registrasi ini Tidak ada.", []);
        } 
        try{
            
            DB::beginTransaction();
            // validator 
            $validator = Validator::make($request->all(), [ 
                "NoRegistrasi" => "required"
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 200);
            }

        
            if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasi($request)->count()) {
                //response 
                return $this->sendError("Antrian Atas No. Registrasi ini sudah ada.", []);
            } 
            // cari max antrian
            $datenow = Carbon::now()->toDateString();
       
            $maxnumberantrian = $this->aAntrianFarmasiRepository->getMaxAntrian($datenow);
                 if($maxnumberantrian){
                    $idno_urutantrian = $maxnumberantrian->NoAntrianList;
                    $idno_urutantrian++;
                 }else{
                    $idno_urutantrian=1;
                 }
            $registration = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi);
            if ($registration->count() > 0) { 
                $datareg = $registration->first();
                $NoEpisode = $datareg->NoEpisode; 
                $NoRegistrasi = $datareg->NoRegistrasi; 
                $NoMR =  $datareg->NoMR; 
                $NoAntrianPoli = $datareg->NoAntrianAll; 
                $PatientName = $datareg->PatientName;
                $StatusAntrean = 'CREATED';
                $DateCreated = Carbon::now();
                $NoAntrianList = $idno_urutantrian;
                $IDPoliOrder = $datareg->IdUnit;
                $NamaPoliOrder= $datareg->NamaUnit;
                $IDDokter= $datareg->IdDokter;
                $NamaDokter= $datareg->NamaDokter;
            } else {
                return $this->sendError("Data Registrasi tidak di temukan.", []);
            }
                // antrian header
                $this->aAntrianFarmasiRepository->CreateAntrian($NoEpisode,
                                                    $NoRegistrasi,$NoMR,
                                                    $NoAntrianPoli,$NoAntrianList,
                                                    $StatusAntrean,$DateCreated,$PatientName,$request->IdUnitFarmasi
                                                    ,  $IDPoliOrder, $NamaPoliOrder, $IDDokter, $NamaDokter,$request->JenisResep,$request->NoResep);
                
                // antrian history
                $this->aAntrianFarmasiRepository->CreateHistoryAntrian($NoRegistrasi,$StatusAntrean,$DateCreated,$request->NoResep);
            
                DB::commit();
                return $this->sendResponse([], "Antrian Berhasil Di tambahkan.");
                
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function CreateAntrianFarmasiNew(Request $request)
    {
        if ($request->NoRegistrasi == "") {
            return $this->sendError("SCAN QR CODE GAGAL.. !!! No. Registrasi Kosong. Sialhkan SCAN QR kembali.", []);
        } 
        if ($request->IdUnitFarmasi == "") {
            return $this->sendError("SCAN QR CODE GAGAL.. !!! Unit Farmasi Kosong.", []);
        } 
        
        try{
            
            DB::beginTransaction();
        
            if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasi($request)->count() < 1) {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Antrian Atas No. Registrasi ini tidak ada.", []);
            } 

            //check apakah sudah checkin ?
            if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiCheckin($request)->count() > 0) {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Antrian Obat Atas No. Registrasi ini sudah Check In, Silahkan menunggu Antrian Obat Anda.", []);
            } 

            // Get Resep by Noregister
            if ($this->aAntrianFarmasiRepository->getResepObatbyNoRegister($request)->count() < 1) {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Resep tidak ada.", []);
            } 
            // cari max antrian
            $datenow = Carbon::now()->toDateString();
        
            $registration = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi);
            if ($registration->count() > 0) { 
                $datareg = $registration->first();
                $NoEpisode = $datareg->NoEpisode; 
                $NoRegistrasi = $datareg->NoRegistrasi; 
                $NoMR =  $datareg->NoMR; 
                $NoAntrianPoli = $datareg->NoAntrianAll; 
                $PatientName = $datareg->PatientName;
                $StatusAntrean = 'CREATED';
                $DateCreated = Carbon::now(); 
                $IDPoliOrder = $datareg->IdUnit;
                $NamaPoliOrder= $datareg->NamaUnit;
                $IDDokter= $datareg->IdDokter;
                $NamaDokter= $datareg->NamaDokter;
            } else {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Data Registrasi tidak di temukan.", []);
            }
                // cek dulu apakah dia sudah PROCCESS BELUM
                // JIKA SUDAH DI PROCESS = UPDATE DATE CREATE SAJA, DAN HISTORY MASUKIN DATE CREATE DAN PROCCESSED
                // JIKA BELUM DI PROCCES =  DATE DATE CREATE, DAN STATUS PROCESSS

                $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasi($request);
                foreach ($checkStatusResep as $dataResepbyReg  ) {
                    if ($dataResepbyReg->StatusAntrean == "PROCESSED") {
                        // UPDATE PROCCESS JIKA SUDAH DI REVIEW

                        // LOOPING KODE RESEP - INSERT PROCCESSED
                        $dataresep = $this->aAntrianFarmasiRepository->getResepObatbyNoRegister($request);
                        foreach ($dataresep as $key  ) {
                            # code... 

                            $this->aAntrianFarmasiRepository->CreateAntrianNewSudahReview($request->NoRegistrasi,$DateCreated,$request->IdUnitFarmasi,$key->OrderDate);
                            $this->aAntrianFarmasiRepository->CreateHistoryAntrian($NoRegistrasi,"PROCESSED",$DateCreated,$key->OrderID);
                        } 
                    } else{
    
                          // UPDATE PROCCESS JIKA BELUM DI REVIEW
                            $this->aAntrianFarmasiRepository->CreateAntrianNew($request->NoRegistrasi,"CREATED",$DateCreated,$request->IdUnitFarmasi);
                            // LOOPING KODE RESEP
                            $dataresep = $this->aAntrianFarmasiRepository->getResepObatbyNoRegister($request);
                            foreach ($dataresep as $key  ) {
                                # code... 
                                $this->aAntrianFarmasiRepository->CreateAntrianNewSudahReview($request->NoRegistrasi,$DateCreated,$request->IdUnitFarmasi,$key->OrderDate);
                                $this->aAntrianFarmasiRepository->CreateHistoryAntrian($NoRegistrasi,$StatusAntrean,$DateCreated,$key->OrderID);
                            } 
                    }  
                }
                
                
                DB::commit();
                return $this->sendResponse([], "SCAN QR CODE Antrian Farmasi berhasil, Silahkan menunggu Antrian Obat Anda.");
                
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function UpdateAntrianFarmasi(Request $request)
    {
        try{
            
            DB::beginTransaction();
            // validator 
            $validator = Validator::make($request->all(), [ 
                "NoRegistrasi" => "required",
                "NoResep" => "required",
                "StatusResep" => "required"
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 200);
            }
        
            if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasi($request)->count() < 1) {
                return $this->sendError("Antrian Atas No. Registrasi ini Tidak ada.", []);
            } 
            if($request->StatusResep == "CLOSED"){
                return $this->sendError("Status Closed Hanya Bisa digunakan Pada Form Penyerahan Resep.", []);
            }
                if($request->StatusResep == "PROCESSED"){
                    $this->aAntrianFarmasiRepository->updateStatusProccess($request->NoRegistrasi,"PROCESSED",Carbon::now(),$request->NoResep);
                    $this->aAntrianFarmasiRepository->CreateHistoryAntrian($request->NoRegistrasi,"PROCESSED",Carbon::now(),$request->NoResep);
                }elseif($request->StatusResep == "FINISHED"){
                    // $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->first();
                    // if ($checkStatusResep->DatePacked == null) {
                    //     return $this->sendError("Verifikasi Resep DIKEMAS belum dilakukan, silahkan verifikasi Resep DIKEMAS dahulu.", []);
                    // } else{
                        $this->aAntrianFarmasiRepository->updateStatusFinish($request->NoRegistrasi,"FINISHED",Carbon::now(),$request->NoResep);
                        $this->aAntrianFarmasiRepository->CreateHistoryAntrian($request->NoRegistrasi,"FINISHED",Carbon::now(),$request->NoResep);
                    // } 
                }elseif($request->StatusResep == "CLOSED"){
                    $this->aAntrianFarmasiRepository->updateStatusClose($request->NoRegistrasi,"CLOSED",Carbon::now(),$request->NoResep);
                    $this->aAntrianFarmasiRepository->CreateHistoryAntrian($request->NoRegistrasi,"CLOSED",Carbon::now(),$request->NoResep);
                }
                 
                DB::commit();
                return $this->sendResponse([], "Antrian Berhasil Di Update .");
                
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function ListAntrianFarmasi($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
            "IdUNitFarmasi" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aAntrianFarmasiRepository->ListAntrianFarmasi($request);

            DB::commit();
            return $this->sendResponse($data, 'Data Antrian Obat Farmasi Ditemukan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Antrian Obat Farmasi Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function ListAntrianTV($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
            "IdUNitFarmasi" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aAntrianFarmasiRepository->ListAntrianFarmasiTV($request);

            DB::commit();
            return $this->sendResponse($data, 'Data Antrian Obat Farmasi Ditemukan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Antrian Obat Farmasi Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function ListHistoryAntrianFarmasi($request)
    {
        // validate 
        $request->validate([
            "NoRegistrasi" => "required" 
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aAntrianFarmasiRepository->ListHistoryAntrianFarmasi($request);

            DB::commit();
            return $this->sendResponse($data, 'Data History Antrian Obat Farmasi Ditemukan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data History Antrian Obat Farmasi Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function ListDepoFarmasi()
    {
        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aAntrianFarmasiRepository->ListDepoFarmasi();

            DB::commit();
            return $this->sendResponse($data, 'Data Depo Farmasi Ditemukan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Depo Farmasi Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function UpdateDataVerifikasiAmbilResep(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [ 
            "NoRegistrasi" => "required",
            "Nama" => "required",
            "NoHandphone" => "required",
            "HubunganDenganPasien" => "required",
            "NoResep" => "required",
            "NoAntrian" => "required",
            "UserCreated" => "required",
            "Keterangan" => "required",
            "IsVerificationOTP" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }
     
        if($request->IsVerificationOTP == ""){
            return $this->sendError("Silahkan Masukan Jenis Verification.", []);
        }
        try{
            DB::beginTransaction(); 
            if($request->IsVerificationOTP == "1"){
                // check OTP nya
                if($request->OTPNumber == ""){
                    return $this->sendError("Silahkan Masukan No. OTP.", []);
                }
                if ($this->aAntrianFarmasiRepository->verifyExpiredOTPbyCodeOTPResep($request)->count() < 1) {
                    //response 
                    return $this->sendError("OTP Expired, Silahkan Generate OTP Kembali.", []);
                } 
            } 
            if($request->ReviewObat['Identitas'] == ""){
                return $this->sendError("Review Obat : Identitas Kosong.", []);
            }
            if($request->ReviewObat['Obat'] == ""){
                return $this->sendError("Review Obat : Obat Kosong.", []);
            }
            if($request->ReviewObat['Dosis'] == ""){
                return $this->sendError("Review Obat : Dosis Kosong.", []);
            }
            if($request->ReviewObat['Rute'] == ""){
                return $this->sendError("Review Obat : Rute Kosong.", []);
            }
            if($request->ReviewObat['Waktu'] == ""){
                return $this->sendError("Review Obat : Waktu Kosong.", []);
            }
            
            // cek Resep
            if ($this->aAntrianFarmasiRepository->getAntrianResepAmbilbyRegistrasi($request)->count()) {
                //response 
                return $this->sendError("Resep Sudah di Ambil, Data Tidak Bisa di Edit.", []);
            } 
            $VerifyResep=$this->aAntrianFarmasiRepository->getResepObatbyId($request)->count();
            
            if ($VerifyResep < 1) {
                //response 
                return $this->sendError("No. Order Resep Tidak di temukan, Silahkan Cek Modul Farmasi.", []);
            } 

            // cek antrian Resep
            if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiCheckin($request)->count() < 1 ) {
                //response 
                return $this->sendError("Pasien Belum melakukan Scan QR Code, Silahkan Scan QR Code terlebih Dahulu.", []);
            } 

                // antrian header
                $this->aAntrianFarmasiRepository->UpdateDataVerifikasiAmbilResep(
                                                $request->NoResep,$request->NoRegistrasi,
                                                $request->NoAntrian,Carbon::now(),
                                                $request->UserCreated,$request->Nama,$request->NoHandphone,
                                                $request->HubunganDenganPasien,$request->Keterangan );
                // update Review Obat
                $this->aAntrianFarmasiRepository->UpdateReviewObat($request->NoResep,
                                                                    $request->ReviewObat['Identitas'],
                                                                    $request->ReviewObat['Obat'],
                                                                    $request->ReviewObat['Dosis'],
                                                                    $request->ReviewObat['Rute'],
                                                                    $request->ReviewObat['Waktu']);
                // antrian history
                $this->aAntrianFarmasiRepository->updateStatusClose($request->NoRegistrasi,"CLOSED",Carbon::now(), $request->NoResep);
                $this->aAntrianFarmasiRepository->CreateHistoryAntrian($request->NoRegistrasi,"CLOSED",Carbon::now(),$request->NoResep);

            if($request->IsVerificationOTP == "1"){
                // set Expired OTP
                $this->aAntrianFarmasiRepository->UpdateOtpFarmasibyKodeOTP($request->NoResep,Carbon::now(), $request->OTPNumber);

                 // verified closed
                 $this->aAntrianFarmasiRepository->verifiedStatusClose($request->OTPNumber,Carbon::now(), $request->NoResep,$request->NoRegistrasi);
            }else{
                // verified closed
                $this->aAntrianFarmasiRepository->verifiedStatusClose("0",Carbon::now(), $request->NoResep,$request->NoRegistrasi);
            }

                DB::commit();
                return $this->sendResponse([], "Resep Obat Pasien Berhasil di berikan, Status Resep Closed.");
                
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function UpdateDataVerifikasi(Request $request)
    {
        if($request->NoRegistrasi == ""){
            return $this->sendError("Silahkan Masukan No. Registrasi.", []);
        }
        if($request->NoResep == ""){
            return $this->sendError("Silahkan Masukan No. Resep.", []);
        }
        if($request->Username == ""){
            return $this->sendError("Silahkan Masukan No. PIN/User Login Anda.", []);
        }
        if($request->Nama == ""){
            return $this->sendError("Silahkan Masukan Nama Login Anda.", []);
        }
        if($request->StatusVerifikasi == ""){
            return $this->sendError("Silahkan Masukan Jenis Verifikasi.", []);
        }
        if($request->TanggalVerifikasi == ""){
            return $this->sendError("Silahkan Masukan Tanggal Verifikasi.", []);
        }
            // cek registrasi 
            $registration = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi);
            if ($registration->count() < 1) { 
                return $this->sendError("No. Registrasi Pasien Tidak di Temukan.", []);
            }

            // no resep
            $VerifyResep=$this->aAntrianFarmasiRepository->getResepObatbyId($request)->count();
            if ($VerifyResep < 1) {
                //response 
                return $this->sendError("No. Order Resep Tidak di temukan, Silahkan Cek Modul Farmasi.", []);
            } 
            
            // // cek pin user 
            // $registration = $this->userLoginRepository->getLoginSimrsPin($request->NoPIN);
            // if ($registration->count() > 0) { 
            //     $datareg = $registration->first();
            //     $NamaDepan = $datareg->NamaDepan;  
            // } else {
            //     return $this->sendError("Data PIN User tidak di temukan.", []);
            // }   
        try{
            DB::beginTransaction(); 

            // cek antrian Resep
            // if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiCheckin($request)->count() < 1 ) {
            //     //response 
            //     return $this->sendError("Antrian Resep Tidak ditemukan/Belum CheckIn.", []);
            // } 

                //update Verifikasi 
                if($request->StatusVerifikasi == "DIAMBIL"){
                    $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->first();
                    //dd($checkStatusResep->StatusAntrean);
                    if ($checkStatusResep->StatusAntrean <> 'PROCESSED') {
                        return $this->sendError("Status Resep Belum di Review, Silahkan Review Resep terlebih Dahulu.", []);
                    } else{
                        if ($checkStatusResep->DateTaken <> null) {
                            return $this->sendError("Resep sudah di Verifikasi DIAMBIL.", []);
                        } else{
                            $this->aAntrianFarmasiRepository->UpdateDataVerifikasiResepDiAmbil( 
                                $request->NoResep,$request->NoRegistrasi,Carbon::now(),$request->Nama);
                        }
                    }
                }elseif($request->StatusVerifikasi == "DIPERIKSA"){
                    $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->first();
                    if ($checkStatusResep->DateTaken == null) {
                        return $this->sendError("Verifikasi Resep DIAMBIL belum dilakukan, silahkan verifikasi Resep DIAMBIL dahulu.", []);
                    } else{
                        if ($checkStatusResep->DateChecked <> null) {
                            return $this->sendError("Resep sudah di Verifikasi DIPERIKSA.", []);
                        } else{
                            $this->aAntrianFarmasiRepository->UpdateDataVerifikasiResepDiPeriksa(
                                $request->NoResep,$request->NoRegistrasi,Carbon::now(),$request->Nama);
                        }
                    }
                }elseif($request->StatusVerifikasi == "DIKEMAS"){
                    $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->first();
                    if ($checkStatusResep->DateChecked == null) {
                        return $this->sendError("Verifikasi Resep DIPERIKSA belum dilakukan, silahkan verifikasi Resep DIPERIKSA dahulu.", []);
                    } else{
                        if ($checkStatusResep->DatePacked <> null) {
                            return $this->sendError("Resep sudah di Verifikasi DIKEMAS.", []);
                        } else{
                            $this->aAntrianFarmasiRepository->UpdateDataVerifikasiResepDikemas(
                                $request->NoResep,$request->NoRegistrasi,Carbon::now(),$request->Nama);
                        }
                       
                    } 
                }else{
                    return $this->sendError("Status Verifikasi Invalid.", []);
                } 

                DB::commit();
                return $this->sendResponse([], "Resep Obat Pasien Berhasil di Verifikasi :  " . $request->StatusVerifikasi . ", Petugas : ".$request->Nama);
                
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function ListAntrianFinish($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
            "IdUNitFarmasi" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aAntrianFarmasiRepository->ListAntrianFinish($request);

            DB::commit();
            return $this->sendResponse($data, 'Data Antrian Obat Farmasi Ditemukan !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Antrian Obat Farmasi Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function SendVerificationNumber($request)
    {   

        // validate 
        if ($request->NoHandphone == "") {  
            return $this->sendError("No. Handphone Kosong.", []);
        }
        if ($request->NoResep == "") {  
            return $this->sendError("No. Resep Kosong.", []);
        }
     
        try {
            // Db Transaction
            DB::beginTransaction();
            $replacenumberhp = $this->ConvertMobilePhoneNumber($request->NoHandphone);
            $genOtpNumber = $this->genOTP();
            $generateTokenWapin = $this->GuzzleClientPostTokenWapin();
            $body = '{
                "client_id": "0410",
                "project_id": "2805",
                "type": "otp_rsyarsi",
                "recipient_number": "'. $replacenumberhp . '",
                "language_code": "id",
                "params": {
                    "1": "' . $genOtpNumber . '"
                },
                "button": {
                "url" : "",
                "reply_payload":{
                                "1" : "",
                                "2" : "",
                                "3" : ""
                                }
                } 
                }';
                $sendwa = $this->GuzzleClientPostWhatsapp($generateTokenWapin,$body);
                $JsonData = json_encode($sendwa);
                $convert = json_decode($JsonData,TRUE);
 
           
            if($convert['status'] == "200"){
                $this->aAntrianFarmasiRepository->UpdateOtpFarmasi($request->NoResep,Carbon::now());
                $this->aAntrianFarmasiRepository->InsertOtpFarmasi($genOtpNumber,$request->NoResep,Carbon::now());
                DB::commit();
                return $this->sendResponse([], 'OTP telah dikirim, silahkan Masukan Kode OTP Anda.'  );
            }else{
                return $this->sendResponse([], 'OTP Gagal dikirim. Periksa Kembali Nomor Anda ! ');
            }
           
           // return $this->sendResponse($sendwa, 'Data Antrian Obat Farmasi Ditemukan ! '. $genOtpNumber . ' - ' . $generateTokenWapin );
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Antrian Obat Farmasi Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function RuningText($request)
    {
        try {
            $dataRuningText = $this->aAntrianFarmasiRepository->RuningTextFarmasi($request);
            if ($dataRuningText->count() < 1) {
                //response 
                return $this->sendError("Running Text Farmasi NotFound.", []);
            } 
             
                return $this->sendResponse($dataRuningText->first(), "Data Runing Text Farmasi Ditemukan.");

        } catch (Exception $e) {
          
            Log::info($e->getMessage());
            return $this->sendError('Data Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function ViewResepMedrecbyDate($request)
    {
        try {
            $dataRuningText = $this->aAntrianFarmasiRepository->ViewResepMedrecbyDate($request);
            if ($dataRuningText->count() < 1) {
                //response 
                return $this->sendError("Data Antrian Resep Belum Ada.", []);
            } 
             
                return $this->sendResponse($dataRuningText, "Data Antrian Resep Farmasi Ditemukan.");

        } catch (Exception $e) {
          
            Log::info($e->getMessage());
            return $this->sendError('Data Tidak Ditemukan !', $e->getMessage());
        }
    }
    public function ViewHistoryResepMedrecbyNoResep($request)
    {
        try {
            $dataRuningText = $this->aAntrianFarmasiRepository->ViewHistoryResepMedrecbyNoResep($request);
            if ($dataRuningText->count() < 1) {
                //response 
                return $this->sendError("Data Antrian Resep Belum Ada.", []);
            } 
             
                return $this->sendResponse($dataRuningText, "Data Antrian Resep Farmasi Ditemukan.");

        } catch (Exception $e) {
          
            Log::info($e->getMessage());
            return $this->sendError('Data Tidak Ditemukan !', $e->getMessage());
        }
    }

    public function CreateAntrianFarmasiNewv2(Request $request)
    {
        if ($request->NoRegistrasi == "") {
            return $this->sendError("SCAN QR CODE GAGAL.. !!! No. Registrasi Kosong. Sialhkan SCAN QR kembali.", []);
        } 
        if ($request->IdUnitFarmasi == "") {
            return $this->sendError("SCAN QR CODE GAGAL.. !!! Unit Farmasi Kosong.", []);
        } 
        
        try{
            
            DB::beginTransaction();
        
            if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasi($request)->count() < 1) {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Antrian Atas No. Registrasi ini tidak ada.", []);
            } 

            //check apakah sudah checkin ?
            if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiCheckin($request)->count() > 0) {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Antrian Obat Atas No. Registrasi ini sudah Check In, Silahkan menunggu Antrian Obat Anda.", []);
            } 

            // Get Resep by Noregister
            if ($this->aAntrianFarmasiRepository->getResepObatbyNoRegisterv2($request)->count() < 1) {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Resep tidak ada.", []);
            } 
            // cari max antrian
            $datenow = Carbon::now()->toDateString();
        
            $registration = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi);
            if ($registration->count() > 0) { 
                $datareg = $registration->first();
                $NoEpisode = $datareg->NoEpisode; 
                $NoRegistrasi = $datareg->NoRegistrasi; 
                $NoMR =  $datareg->NoMR; 
                $NoAntrianPoli = $datareg->NoAntrianAll; 
                $PatientName = $datareg->PatientName;
                $StatusAntrean = 'CREATED';
                $DateCreated = Carbon::now(); 
                $IDPoliOrder = $datareg->IdUnit;
                $NamaPoliOrder= $datareg->NamaUnit;
                $IDDokter= $datareg->IdDokter;
                $NamaDokter= $datareg->NamaDokter;
            } else {
                return $this->sendError("SCAN QR CODE GAGAL.. !!! Data Registrasi tidak di temukan.", []);
            }
                // cek dulu apakah dia sudah PROCCESS BELUM
                // JIKA SUDAH DI PROCESS = UPDATE DATE CREATE SAJA, DAN HISTORY MASUKIN DATE CREATE DAN PROCCESSED
                // JIKA BELUM DI PROCCES =  DATE DATE CREATE, DAN STATUS PROCESSS

                $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasi($request);
                foreach ($checkStatusResep as $dataResepbyReg  ) {
                    if ($dataResepbyReg->StatusAntrean == "PROCESSED") {
                        // UPDATE PROCCESS JIKA SUDAH DI REVIEW

                        // LOOPING KODE RESEP - INSERT PROCCESSED
                        $dataresep = $this->aAntrianFarmasiRepository->getResepObatbyNoRegister($request);
                        foreach ($dataresep as $key  ) {
                            # code... 

                            $this->aAntrianFarmasiRepository->CreateAntrianNewSudahReview($request->NoRegistrasi,$DateCreated,$request->IdUnitFarmasi,$key->OrderDate);
                            $this->aAntrianFarmasiRepository->CreateHistoryAntrian($NoRegistrasi,"PROCESSED",$DateCreated,$key->OrderID);
                        } 
                    } else{
    
                          // UPDATE PROCCESS JIKA BELUM DI REVIEW
                            $this->aAntrianFarmasiRepository->CreateAntrianNew($request->NoRegistrasi,"CREATED",$DateCreated,$request->IdUnitFarmasi);
                            // LOOPING KODE RESEP
                            $dataresep = $this->aAntrianFarmasiRepository->getResepObatbyNoRegister($request);
                            foreach ($dataresep as $key  ) {
                                # code... 
                                $this->aAntrianFarmasiRepository->CreateAntrianNewSudahReview($request->NoRegistrasi,$DateCreated,$request->IdUnitFarmasi,$key->OrderDate);
                                $this->aAntrianFarmasiRepository->CreateHistoryAntrian($NoRegistrasi,$StatusAntrean,$DateCreated,$key->OrderID);
                            } 
                    }  
                }
                
                
                DB::commit();
                return $this->sendResponse([], "SCAN QR CODE Antrian Farmasi berhasil, Silahkan menunggu Antrian Obat Anda.");
                
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 

    public function UpdateDataVerifikasiV2(Request $request)
    {
        if($request->NoRegistrasi == ""){
            return $this->sendError("Silahkan Masukan No. Registrasi.", []);
        }
        if($request->NoResep == ""){
            return $this->sendError("Silahkan Masukan No. Resep.", []);
        }
        if($request->Username == ""){
            return $this->sendError("Silahkan Masukan No. PIN/User Login Anda.", []);
        }
        if($request->Nama == ""){
            return $this->sendError("Silahkan Masukan Nama Login Anda.", []);
        }
        if($request->StatusVerifikasi == ""){
            return $this->sendError("Silahkan Masukan Jenis Verifikasi.", []);
        }
        if($request->TanggalVerifikasi == ""){
            return $this->sendError("Silahkan Masukan Tanggal Verifikasi.", []);
        }
            // cek registrasi 
            $registration = $this->visitRepository->getRegistrationRajalbyNoreg($request->NoRegistrasi);
            if ($registration->count() < 1) { 
                return $this->sendError("No. Registrasi Pasien Tidak di Temukan.", []);
            }

            // no resep
            $VerifyResep=$this->aAntrianFarmasiRepository->getResepObatbyIdV2($request)->count();
            if ($VerifyResep < 1) {
                //response 
                return $this->sendError("No. Order Resep Tidak di temukan, Silahkan Cek Modul Farmasi.", []);
            } 
            
            // // cek pin user 
            // $registration = $this->userLoginRepository->getLoginSimrsPin($request->NoPIN);
            // if ($registration->count() > 0) { 
            //     $datareg = $registration->first();
            //     $NamaDepan = $datareg->NamaDepan;  
            // } else {
            //     return $this->sendError("Data PIN User tidak di temukan.", []);
            // }   
        try{
            DB::beginTransaction(); 

            // cek antrian Resep
            // if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiCheckin($request)->count() < 1 ) {
            //     //response 
            //     return $this->sendError("Antrian Resep Tidak ditemukan/Belum CheckIn.", []);
            // } 

                //update Verifikasi 
                if($request->StatusVerifikasi == "DIAMBIL"){
                    if ($this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->count() < 1) {
                        return $this->sendError("Pasien Belum melakukan Scan QR Code, Silahkan Scan QR Code terlebih Dahulu.", []);
                    } 
                    
                    $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->first();
                    //dd($checkStatusResep->StatusAntrean);
                    if ($checkStatusResep->StatusAntrean <> 'PROCESSED') {
                        return $this->sendError("Status Resep Belum di Review, Silahkan Review Resep terlebih Dahulu.", []);
                    } else{
                        if ($checkStatusResep->DateTaken <> null) {
                            return $this->sendError("Resep sudah di Verifikasi DIAMBIL.", []);
                        } else{
                            $this->aAntrianFarmasiRepository->UpdateDataVerifikasiResepDiAmbil( 
                                $request->NoResep,$request->NoRegistrasi,Carbon::now(),$request->Nama);
                        }
                    }
                }elseif($request->StatusVerifikasi == "DIPERIKSA"){
                    $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->first();
                    if ($checkStatusResep->DateTaken == null) {
                        return $this->sendError("Verifikasi Resep DIAMBIL belum dilakukan, silahkan verifikasi Resep DIAMBIL dahulu.", []);
                    } else{
                        if ($checkStatusResep->DateChecked <> null) {
                            return $this->sendError("Resep sudah di Verifikasi DIPERIKSA.", []);
                        } else{
                            $this->aAntrianFarmasiRepository->UpdateDataVerifikasiResepDiPeriksa(
                                $request->NoResep,$request->NoRegistrasi,Carbon::now(),$request->Nama);
                        }
                    }
                }elseif($request->StatusVerifikasi == "DIKEMAS"){
                    $checkStatusResep = $this->aAntrianFarmasiRepository->getAntrianFarmasibyRegistrasiNoresep($request)->first();
                    if ($checkStatusResep->DateChecked == null) {
                        return $this->sendError("Verifikasi Resep DIPERIKSA belum dilakukan, silahkan verifikasi Resep DIPERIKSA dahulu.", []);
                    } else{
                        if ($checkStatusResep->DatePacked <> null) {
                            return $this->sendError("Resep sudah di Verifikasi DIKEMAS.", []);
                        } else{
                            $this->aAntrianFarmasiRepository->UpdateDataVerifikasiResepDikemas(
                                $request->NoResep,$request->NoRegistrasi,Carbon::now(),$request->Nama);
                        }
                       
                    } 
                }else{
                    return $this->sendError("Status Verifikasi Invalid.", []);
                } 

                DB::commit();
                return $this->sendResponse([], "Resep Obat Pasien Berhasil di Verifikasi :  " . $request->StatusVerifikasi . ", Petugas : ".$request->Nama);
                
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
}
