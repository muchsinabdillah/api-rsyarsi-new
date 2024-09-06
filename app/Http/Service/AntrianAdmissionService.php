<?php

namespace App\Http\Service;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\AutoNumberTrait;
use App\Traits\VerificationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\ApiExternalConsumeTrait; 
use App\Http\Repository\UserRepositoryImpl; 
use App\Http\Repository\bAntrianAdmissionRepositoryImpl;
use App\Http\Repository\cMasterDataAntrianRepositoryImpl;

date_default_timezone_set("Asia/Jakarta");
class AntrianAdmissionService extends Controller
{
    use VerificationTrait;
    use ApiExternalConsumeTrait;
    use AutoNumberTrait;
    private $masterAntrianRepository;
    private $trsAntrianAdmissionRepository; 
    private $userLoginRepository;
    public function __construct(
        cMasterDataAntrianRepositoryImpl $masterAntrianRepository,
        bAntrianAdmissionRepositoryImpl $trsAntrianAdmissionRepository,
        UserRepositoryImpl $userLoginRepository
    )
    {
        $this->masterAntrianRepository = $masterAntrianRepository;
        $this->trsAntrianAdmissionRepository = $trsAntrianAdmissionRepository; 
        $this->userLoginRepository = $userLoginRepository; 
    }

    public function CreateAntrian(Request $request)
    {
        if ($request->Jenis_Jaminan == "") {
            return $this->sendError("Silahkan Masukan Jenis Antrian ( UMUM/PERUSAHAAN/BPJS. )", []);
        } 
        if ($request->FloorID == "") {
            return $this->sendError("Silahkan Masukan Lantai Pendaftarn.", []);
        } 
        if ($request->Jenis_Jaminan == "UM") {
           $namaJenisJaminan = "UMUM";
        }elseif ($request->Jenis_Jaminan == "BP") {
            $namaJenisJaminan = "BPJS";
        }else{
            $namaJenisJaminan = "ASURANSI/PERUSAHAAN";
        }
        try{
            DB::connection('sqlsrv3')->beginTransaction();
            DB::connection('sqlsrv2')->beginTransaction();
            // cari max antrian
            $datenow = Carbon::now()->toDateString();  
            $autonumber = $this->AntrianAdmission($datenow, $request->Jenis_Jaminan);
 
            $this->trsAntrianAdmissionRepository->CreateAntrianAdmission($request,$request->Jenis_Jaminan.$autonumber,$datenow,$autonumber);
          
            DB::connection('sqlsrv3')->commit();
            DB::connection('sqlsrv2')->commit();

            $response = array(
                'namaJenisJaminan' => $namaJenisJaminan, // Set array status dengan success     
                'NoAntrian' => $autonumber, // Set array status dengan success      
            );
            return $this->sendResponse($response, "Antrian Admission Berhasil Di tambahkan. No. Antrian : ".$request->Jenis_Jaminan.$autonumber);   
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            DB::connection('sqlsrv2')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function CreateAntrianAdmissionAPM(Request $request)
    {
     
        if ($request->FloorID == "") {
            return $this->sendError("Silahkan Masukan Lantai Pendaftarn.", []);
        } 
        if ($request->Jenis_Jaminan == "UM") {
           $namaJenisJaminan = "UMUM";
        }elseif ($request->Jenis_Jaminan == "BP") {
            $namaJenisJaminan = "BPJS";
        }else{
            $namaJenisJaminan = "ASURANSI/PERUSAHAAN";
        }
        try{
            DB::connection('sqlsrv3')->beginTransaction();
            DB::connection('sqlsrv2')->beginTransaction();
            // cari max antrian
            $datenow = Carbon::now()->toDateString();  
            $autonumber = $this->AntrianAdmissionAPM($datenow);
 
            $this->trsAntrianAdmissionRepository->CreateAntrianAdmissionAPM($request,$autonumber,$datenow,$autonumber);
          
            DB::connection('sqlsrv3')->commit();
            DB::connection('sqlsrv2')->commit();

            $response = array(    
                'NoAntrian' => $autonumber, // Set array status dengan success      
            );
            return $this->sendResponse($response, "Antrian Admission Berhasil Di tambahkan. No. Antrian : ".$request->Jenis_Jaminan.$autonumber);   
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            DB::connection('sqlsrv2')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function PanggilAntrian(Request $request)
    {
        if ($request->IDTrsAntrian == "") {
            return $this->sendError("Silahkan Masukan Id Transaksi Antrian Admission.", []);
        } 
     
        if ($request->Username == "") {
            return $this->sendError("Silahkan Masukan Username Anda.", []);
        } 
        if ($request->ConterID == "") {
            return $this->sendError("Silahkan Masukan Counter ID.", []);
        } 
        if ($request->StatusAntrian == "") {
            return $this->sendError("Silahkan Masukan Status Antrian.", []);
        }  
        if ( $request->StatusAntrian <> "CALLED" ) {
            return $this->sendError("Status Antrian Invalid. ".  $request->StatusAntrian , []);
        } 
        try{
            DB::connection('sqlsrv3')->beginTransaction();
            DB::connection('sqlsrv2')->beginTransaction();
            // cari max antrian
            $datenow = Carbon::now();  

            $verifyAntrianAmdisionbyID = $this->trsAntrianAdmissionRepository->getAntrianAdmissionbyID($request->IDTrsAntrian);
            if ($verifyAntrianAmdisionbyID->count() < 1 ) {
                return $this->sendError("ID Transaksi Antrian Tidak diTemukan.", []);
            } 
            $verifyUsername = $this->userLoginRepository->getLoginSimrswithUserNameOnly($request->Username);
            if ($verifyUsername->count() < 1 ) {
                return $this->sendError("Username tidak ditemukan.", []);
            } 
            $verifyAntrianCounter = $this->masterAntrianRepository->ViewbyIdAntrianCounter($request->ConterID);
            if ($verifyAntrianCounter->count() < 1 ) {
                return $this->sendError("ID Counter tidak ditemukan.", []);
            } 

            $this->trsAntrianAdmissionRepository->PanggilAntrian($request, $datenow);
          
            DB::connection('sqlsrv3')->commit();
            DB::connection('sqlsrv2')->commit();
            return $this->sendResponse([], "Antrian berhasil di Panggil. ");   
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            DB::connection('sqlsrv2')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function ProccesedAntrian(Request $request)
    {
        if ($request->IDTrsAntrian == "") {
            return $this->sendError("Silahkan Masukan Id Transaksi Antrian Admission.", []);
        } 
     
        if ($request->Username == "") {
            return $this->sendError("Silahkan Masukan Username Anda.", []);
        } 
        if ($request->ConterID == "") {
            return $this->sendError("Silahkan Masukan Counter ID.", []);
        } 
        if ($request->StatusAntrian == "") {
            return $this->sendError("Silahkan Masukan Status Antrian.", []);
        }  
        if ( $request->StatusAntrian <> "PROCCESSED" ) {
            return $this->sendError("Status Antrian Invalid. ".  $request->StatusAntrian , []);
        } 
        try{
            DB::connection('sqlsrv3')->beginTransaction();
            DB::connection('sqlsrv2')->beginTransaction();
            // cari max antrian
            $datenow = Carbon::now();  

            $verifyAntrianAmdisionbyID = $this->trsAntrianAdmissionRepository->getAntrianAdmissionbyID($request->IDTrsAntrian);
            if ($verifyAntrianAmdisionbyID->count() < 1 ) {
                return $this->sendError("ID Transaksi Antrian Tidak diTemukan.", []);
            } 
            $verifyUsername = $this->userLoginRepository->getLoginSimrswithUserNameOnly($request->Username);
            if ($verifyUsername->count() < 1 ) {
                return $this->sendError("Username tidak ditemukan.", []);
            } 
            $verifyAntrianCounter = $this->masterAntrianRepository->ViewbyIdAntrianCounter($request->ConterID);
            if ($verifyAntrianCounter->count() < 1 ) {
                return $this->sendError("ID Counter tidak ditemukan.", []);
            } 

            $this->trsAntrianAdmissionRepository->ProccesedAntrian($request, $datenow);
          
            DB::connection('sqlsrv3')->commit();
            DB::connection('sqlsrv2')->commit();
            return $this->sendResponse([], "Antrian berhasil di Proses. ");   
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            DB::connection('sqlsrv2')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function HoldAntrian(Request $request)
    {
        if ($request->IDTrsAntrian == "") {
            return $this->sendError("Silahkan Masukan Id Transaksi Antrian Admission.", []);
        } 
     
        if ($request->Username == "") {
            return $this->sendError("Silahkan Masukan Username Anda.", []);
        } 
        if ($request->ConterID == "") {
            return $this->sendError("Silahkan Masukan Counter ID.", []);
        } 
        if ($request->StatusAntrian == "") {
            return $this->sendError("Silahkan Masukan Status Antrian.", []);
        }  
        if ( $request->StatusAntrian <> "HOLD" ) {
            return $this->sendError("Status Antrian Invalid. ".  $request->StatusAntrian , []);
        } 
        try{
            DB::connection('sqlsrv3')->beginTransaction();
            DB::connection('sqlsrv2')->beginTransaction();
            // cari max antrian
            $datenow = Carbon::now();  

            $verifyAntrianAmdisionbyID = $this->trsAntrianAdmissionRepository->getAntrianAdmissionbyID($request->IDTrsAntrian);
            if ($verifyAntrianAmdisionbyID->count() < 1 ) {
                return $this->sendError("ID Transaksi Antrian Tidak diTemukan.", []);
            } 
            $verifyUsername = $this->userLoginRepository->getLoginSimrswithUserNameOnly($request->Username);
            if ($verifyUsername->count() < 1 ) {
                return $this->sendError("Username tidak ditemukan.", []);
            } 
            $verifyAntrianCounter = $this->masterAntrianRepository->ViewbyIdAntrianCounter($request->ConterID);
            if ($verifyAntrianCounter->count() < 1 ) {
                return $this->sendError("ID Counter tidak ditemukan.", []);
            } 

            $this->trsAntrianAdmissionRepository->HoldAntrian($request, $datenow);
          
            DB::connection('sqlsrv3')->commit();
            DB::connection('sqlsrv2')->commit();
            return $this->sendResponse([], "Antrian berhasil di Hold. ");   
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            DB::connection('sqlsrv2')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function ClosedAntrian(Request $request)
    {
        if ($request->IDTrsAntrian == "") {
            return $this->sendError("Silahkan Masukan Id Transaksi Antrian Admission.", []);
        } 
     
        if ($request->Username == "") {
            return $this->sendError("Silahkan Masukan Username Anda.", []);
        } 
        if ($request->ConterID == "") {
            return $this->sendError("Silahkan Masukan Counter ID.", []);
        } 
        if ($request->StatusAntrian == "") {
            return $this->sendError("Silahkan Masukan Status Antrian.", []);
        }  
        if ( $request->StatusAntrian <> "CLOSED" ) {
            return $this->sendError("Status Antrian Invalid. ".  $request->StatusAntrian , []);
        } 
        try{
            DB::connection('sqlsrv3')->beginTransaction();
            DB::connection('sqlsrv2')->beginTransaction();
            // cari max antrian
            $datenow = Carbon::now();  

            $verifyAntrianAmdisionbyID = $this->trsAntrianAdmissionRepository->getAntrianAdmissionbyID($request->IDTrsAntrian);
            if ($verifyAntrianAmdisionbyID->count() < 1 ) {
                return $this->sendError("ID Transaksi Antrian Tidak diTemukan.", []);
            } 
            $verifyUsername = $this->userLoginRepository->getLoginSimrswithUserNameOnly($request->Username);
            if ($verifyUsername->count() < 1 ) {
                return $this->sendError("Username tidak ditemukan.", []);
            } 
            $verifyAntrianCounter = $this->masterAntrianRepository->ViewbyIdAntrianCounter($request->ConterID);
            if ($verifyAntrianCounter->count() < 1 ) {
                return $this->sendError("ID Counter tidak ditemukan.", []);
            } 

            $this->trsAntrianAdmissionRepository->ClosedAntrian($request, $datenow);
          
            DB::connection('sqlsrv3')->commit();
            DB::connection('sqlsrv2')->commit();
            return $this->sendResponse([], "Antrian berhasil di Closed. ");   

        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            DB::connection('sqlsrv2')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
        }
    } 
    public function ViewbyIdTrsAntrianAdmission(Request $request){
        try {    
            $response = $this->trsAntrianAdmissionRepository->ViewbyIdTrsAntrianAdmission($request->IDTrsAntrian)->first();
            return $this->sendResponse($response, "Data Antrian Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function ViewbyDateTrsAntrianAdmission(Request $request){
        try {    
            $response = $this->trsAntrianAdmissionRepository->ViewbyDateTrsAntrianAdmission($request);
            return $this->sendResponse($response, "Data Antrian Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function ViewbyDateTrsJaminanAntrianAdmission(Request $request){
        try {    
            $response = $this->trsAntrianAdmissionRepository->ViewbyDateTrsJaminanAntrianAdmission($request);
            return $this->sendResponse($response, "Data Antrian Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function ViewbyFloorTrsAntrianAdmission(Request $request){
        try {    
            $response = $this->trsAntrianAdmissionRepository->ViewbyFloorTrsAntrianAdmission($request);
            return $this->sendResponse($response, "Data Antrian Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
}