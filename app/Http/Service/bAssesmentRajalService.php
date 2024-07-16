<?php 
 
namespace App\Http\Service;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\bAssesmentRajalRepositoryImpl; 
use App\Http\Repository\bVisitRepositoryImpl;

class bAssesmentRajalService extends Controller {
    use AutoNumberTrait;
    private $assesmentRajal; 
    private $registrationRajal; 
    public function __construct(
        bAssesmentRajalRepositoryImpl $assesmentRajal,
        bVisitRepositoryImpl $registrationRajal
        )
    {
        $this->assesmentRajal = $assesmentRajal; 
        $this->registrationRajal = $registrationRajal; 

    }

    public function CreateAssesmentRajal(Request $request){
        try{
            
            DB::connection('sqlsrv5')->beginTransaction();
            DB::connection('sqlsrv6')->beginTransaction();
            if ($request->NoRegistrasi === "" || $request->NoRegistrasi === null) {
                $metadata = array(
                    'message' => "No. Registrasi Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->S_Anamnesa === "" || $request->S_Anamnesa === null) {
                $metadata = array(
                    'message' => "Anamnesa Pasien Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->S_RPD === "" || $request->S_RPD === null) {
                $metadata = array(
                    'message' => "Riwayat Penyakit Dahulu Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->O_PemeriksaanFisik === "" || $request->O_PemeriksaanFisik === null) {
                $metadata = array(
                    'message' => "Pemeriksaan FIsik Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->A_Diagnosa === "" || $request->A_Diagnosa === null) {
                $metadata = array(
                    'message' => "Diagnosa Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            } 

            // Get Data Registration
            $registration = $this->registrationRajal->getRegistrationRajalbyNoreg($request->NoRegistrasi);
            if( $registration->count() < 1){ 
                return $this->sendError("No. Registrasi Invalid.", []); 
            }
            $datareg =  $registration->first();
            $PatientName = $datareg->PatientName;  
            $Gander = $datareg->Gander; 
            $Date_of_birth = $datareg->Date_of_birth; 
            $Address = $datareg->Address; 
            $PatientType = $datareg->PatientType; 
            $CODEUNIT = $datareg->IdUnit; 
            $NamaUnit = $datareg->NamaUnit; 
            $Doctor_1 = $datareg->IdDokter; 
            $First_Name = $datareg->NamaDokter; 
            $NoMR = $datareg->NoMR; 
            $NoEpisode = $datareg->NoEpisode; 
            $NoRegistrasi = $datareg->NoRegistrasi; 
            $Tgl = Carbon::now();

            // cek ada gak assesment dokter
            $assesment = $this->assesmentRajal->getAssesment_Rajal_Dokter($request->NoRegistrasi); 
            if( $assesment->count() > 0){ 
                return $this->sendError("Assesment Dokter sudah ada. Anda Tidak Dapat Melakukan PEnambahan Assesment, Silahkan Update Data Assesment Dokter.", []); 
            }
            // ADD ASSESMENT RAJAL
            $this->assesmentRajal->CreateAssesmentRajal($NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$PatientName,
            $request->KeluhanPasien, $request->S_Anamnesa,$request->S_RPD,
            $request->O_PemeriksaanFisik, $request->A_Diagnosa, $request->P_RencanaTatalaksana,$request->P_InstruksiNonMedis ,
            $First_Name,'Dokter','0',$request->Beratbadan,$request->TinggiBadan,$request->Suhu,
            $request->FrekuensiNafas
            ,$request->TD_Sistol,$request->TD_Distol,$request->FrekuensiNadi,$request->AlatBantu,$request->Prothesa,$request->Cacat); 

            // ADD ASSESMENT RAJAL PERAWAT
            $this->assesmentRajal->CreateAssesmentRajalPerawat($NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$PatientName,
            $request->KeluhanPasien, $request->S_Anamnesa,$request->S_RPD,
            $request->O_PemeriksaanFisik, $request->A_Diagnosa, $request->P_RencanaTatalaksana,$request->P_InstruksiNonMedis ,
            $First_Name,'Dokter','0',$request->Beratbadan,$request->TinggiBadan,$request->Suhu,
            $request->FrekuensiNafas
            ,$request->TD_Sistol,$request->TD_Distol,$request->FrekuensiNadi,$request->AlatBantu,$request->Prothesa,$request->Cacat);

            // ADD CPPT 
            DB::connection('sqlsrv5')->commit();
            DB::connection('sqlsrv6')->commit();
            return $this->sendResponse([] ,"Assesment Berhasil Di Simpan.");  
            
        }catch (Exception $e) { 
            DB::connection('sqlsrv5')->rollBack();
            DB::connection('sqlsrv6')->rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => 'Gagal', // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }
    }   
    public function viewAssesmentRajal(Request $request){
        $data = $this->assesmentRajal->getAssesment_Rajal_Dokter($request->noregistrasi);
            if ($data->count() > 0) {
                $assesment = $data->first();
                return $this->sendResponse($assesment, "Data Assesment Poliklinik ditemukan.");
            } else {
                return $this->sendError("Data Assesment Poliklinik Not Found.", [], 400);
            }
    }
    public function viewAssesmentRajalPerawat(Request $request){
        $data = $this->assesmentRajal->getAssesment_Rajal_Perawat($request->noregistrasi);
            if ($data->count() > 0) {
                $assesment = $data->first();
                return $this->sendResponse($assesment, "Data Assesment Perawat Poliklinik ditemukan.");
            } else {
                return $this->sendError("Data Assesment Poliklinik Not Found.", [], 400);
            }
    }
    public function UpdateAssesmentRajal(Request $request){
        try{
            
            DB::connection('sqlsrv5')->beginTransaction();
            DB::connection('sqlsrv6')->beginTransaction();
            if ($request->ID === "" || $request->ID === null) {
                $metadata = array(
                    'message' => "ID Assesment Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->NoRegistrasi === "" || $request->NoRegistrasi === null) {
                $metadata = array(
                    'message' => "No. Registrasi Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->S_Anamnesa === "" || $request->S_Anamnesa === null) {
                $metadata = array(
                    'message' => "Anamnesa Pasien Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->S_RPD === "" || $request->S_RPD === null) {
                $metadata = array(
                    'message' => "Riwayat Penyakit Dahulu Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->O_PemeriksaanFisik === "" || $request->O_PemeriksaanFisik === null) {
                $metadata = array(
                    'message' => "Pemeriksaan FIsik Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->A_Diagnosa === "" || $request->A_Diagnosa === null) {
                $metadata = array(
                    'message' => "Diagnosa Kosong/Invalid !", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            } 

            // Get Data Registration
            $registration = $this->registrationRajal->getRegistrationRajalbyNoreg($request->NoRegistrasi);
            if( $registration->count() < 1){ 
                return $this->sendError("No. Registrasi Invalid.", []); 
            }
            $datareg =  $registration->first();
            $PatientName = $datareg->PatientName;  
            $Gander = $datareg->Gander; 
            $Date_of_birth = $datareg->Date_of_birth; 
            $Address = $datareg->Address; 
            $PatientType = $datareg->PatientType; 
            $CODEUNIT = $datareg->IdUnit; 
            $NamaUnit = $datareg->NamaUnit; 
            $Doctor_1 = $datareg->IdDokter; 
            $First_Name = $datareg->NamaDokter; 
            $NoMR = $datareg->NoMR; 
          
            $NoEpisode = $datareg->NoEpisode; 
            $NoRegistrasi = $datareg->NoRegistrasi; 
            $Tgl = Carbon::now();

          
            // UPDATE ASSESMENT RAJAL
            $this->assesmentRajal->UpdateAssesmentRajal($request->ID,$NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$PatientName,
            $request->KeluhanPasien, $request->S_Anamnesa,$request->S_RPD,
            $request->O_PemeriksaanFisik, $request->A_Diagnosa, $request->P_RencanaTatalaksana,$request->P_InstruksiNonMedis ,
            $First_Name,'Dokter','0',$request->Beratbadan,$request->TinggiBadan,$request->Suhu,
            $request->FrekuensiNafas
            ,$request->TD_Sistol,$request->TD_Distol,$request->FrekuensiNadi,$request->AlatBantu,$request->Prothesa,$request->Cacat); 

            // ADD ASSESMENT RAJAL PERAWAT
            $this->assesmentRajal->UpdateAssesmentRajalPerawat($request->ID,$NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$PatientName,
            $request->KeluhanPasien, $request->S_Anamnesa,$request->S_RPD,
            $request->O_PemeriksaanFisik, $request->A_Diagnosa, $request->P_RencanaTatalaksana,$request->P_InstruksiNonMedis ,
            $First_Name,'Dokter','0',$request->Beratbadan,$request->TinggiBadan,$request->Suhu,
            $request->FrekuensiNafas
            ,$request->TD_Sistol,$request->TD_Distol,$request->FrekuensiNadi,$request->AlatBantu,$request->Prothesa,$request->Cacat);

            // ADD CPPT 
            DB::connection('sqlsrv5')->commit();
            DB::connection('sqlsrv6')->commit();
            return $this->sendResponse([] ,"Assesment Berhasil Di Simpan.");  
            
        }catch (Exception $e) { 
            DB::connection('sqlsrv5')->rollBack();
            DB::connection('sqlsrv6')->rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => 'Gagal', // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }
    }   
    public function ViewCppt(Request $request){
        if ($request->NoRegistrasi === "" || $request->NoRegistrasi === null) {
            $metadata = array(
                'message' => "No. Registrasi Kosong/Invalid !", // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            ); 
            return  $this->sendErrorNew($metadata,null);
        }
        $data = $this->assesmentRajal->getCPPT($request);
            if ($data->count() > 0) {
                $assesment = $data->first();
                return $this->sendResponse($assesment, "Data Cppt ditemukan.");
            } else {
                return $this->sendError("Data Cppt Not Found.", [], 400);
            }
    }
    
    public function ViewCpptPeriode(Request $request){
        if ($request->NoMR === "" || $request->NoMR === null) {
            $metadata = array(
                'message' => "No. Medrec Kosong/Invalid !", // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            ); 
            return  $this->sendErrorNew($metadata,null);
        }
        if ($request->PeriodeAwal === "" || $request->PeriodeAwal === null) {
            $metadata = array(
                'message' => "Periode Awal Kosong/Invalid !", // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            ); 
            return  $this->sendErrorNew($metadata,null);
        }
        if ($request->PeriodeAkhir === "" || $request->PeriodeAkhir === null) {
            $metadata = array(
                'message' => "Periode Akhir Kosong/Invalid !", // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            ); 
            return  $this->sendErrorNew($metadata,null);
        }
        $data = $this->assesmentRajal->ViewCpptPeriode($request);
            if ($data->count() > 0) {
                $assesment = $data->first();
                return $this->sendResponse($assesment, "Data Cppt ditemukan.");
            } else {
                return $this->sendError("Data Cppt Not Found.", [], 400);
            }
    }
    public function dataoperasiharian(Request $request)
    {
        try {   
            $count = $this->assesmentRajal->dataoperasiharian($request)->count();
            if ($count < 3 ) {  
                return $this->sendResponse($count, "Order Operasi Bisa Di Tambahkan.");
            } else {
                return $this->sendError("Order Operasi Sudah mencapai Limit Batas Tanggal dan Jam yang disepakati yaitu 3 Pasien. Silahkan Ganti tanggal dan Jam. Atau hapus Order Operasi jika tidak jadi. ", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
}
