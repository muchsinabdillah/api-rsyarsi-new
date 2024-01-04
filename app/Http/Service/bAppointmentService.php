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
use App\Http\Repository\aGroupRepositoryImpl;

use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\bAntrianRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\bAppointmentRepositoryImpl;
use App\Http\Repository\bKamarOperasiRepositoryImpl;
use App\Http\Repository\bMedicalRecordRepositoryImpl;
use App\Http\Repository\aScheduleDoctorRepositoryImpl;
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;

class bAppointmentService extends Controller {
    use AutoNumberTrait;
    private $kamaroperasiRepository;
    private $medrecRepository;
    private $doctorRepository;
    private $unitRepository;
    private $appointmenRepository;
    private $scheduleRepository;
    private $antrianRepository;
    private $visitRepository;
    public function __construct(
        bKamarOperasiRepositoryImpl $kamaroperasiRepository,
        bMedicalRecordRepositoryImpl $medrecRepository,
        aDoctorRepositoryImpl $doctorRepository,
        aMasterUnitRepositoryImpl $unitRepository,
        bAppointmentRepositoryImpl $appointmenRepository,
        aScheduleDoctorRepositoryImpl $scheduleRepository,
        bAntrianRepositoryImpl $antrianRepository,
        bVisitRepositoryImpl $visitRepository
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

    }

    public function AmbilAntrian(Request $request){
        try{
            
            DB::beginTransaction();
            // persiapan Insert
            $JenisBayar="5";
            $Company="JKN MOBILE";
            $JenisBoking = "1";
            $datenowcreate = Carbon::now();
            $noteall = null;
            $txNamaPenjamin = "BPJS KESEHATAN";
            $noteall = $txNamaPenjamin . " - MOBILE JKN";
            if ($JenisBayar == "1") {
                $kodejenispayment = "PRIBADI";
            } else if ($JenisBayar == "2") {
                $kodejenispayment = "ASURANSI";
            } else if ($JenisBayar == "5") {
                $kodejenispayment = "JAMINAN PERUSAHAAN";
            }
            $tglbookingfix = $request->tanggalperiksa;
            $tanggalperiksa = date("d-m-Y",strtotime(trim(strip_tags($tglbookingfix))));
            $idkananx = substr($request->tanggalperiksa, 6);
            $idkananxcat = substr($tanggalperiksa, 8);
            $idtengahx = substr($tanggalperiksa, 3, -5);
            $tgl = substr($tanggalperiksa, 0, -8);
            $idbooking =   $tgl . $idtengahx . $idkananxcat;
            

            
            $idkananxres = substr($tanggalperiksa, 8);
            $idtengahxres = substr($tanggalperiksa, 3, -5); //
            $tglres = substr($tanggalperiksa, 0, -8);
            $idbookingres =   $tglres . $idtengahxres . $idkananxres; 
            
            $date = Carbon::parse($request->tanggalperiksa)->locale('id');
             
            $datename = $date->format('l');
            
            $tanggalperiksa = date("d-m-Y",strtotime($request->tanggalperiksa));
            $TrimJamAkhir = substr($request->jampraktek, 6, 5);

            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->tanggalperiksa)) {
                $metadata = array(
                    'message' => "Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorNew($metadata,null);
            }
            //cek kelengkapan data
            if ($request->nik === "" || $request->nik === null) {
                $metadata = array(
                    'message' => "No. KTP Tidak Ditemukan.", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->nohp === "" || $request->nohp === null ) {
                $metadata = array(
                    'message' => "No. Handphone Tidak Ditemukan.", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
               return  $this->sendErrorNew($metadata,null);
            }
            if ($request->kodedokter === "" || $request->kodedokter === null) {
                $metadata = array(
                    'message' => "Kode Dokter Tidak Ditemukan.",
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($request->kodepoli === "" || $request->kodepoli === null) {
                $metadata = array(
                    'message' => "Kode Poliklinik Tidak Ditemukan.", 
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
               return  $this->sendErrorNew($metadata,null);
            }
            if ($request->jampraktek === "" || $request->jampraktek === null) {
                $metadata = array(
                    'message' => 'Jam Praktek Kosong.', // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                 
               return  $this->sendErrorNew($metadata,null);
            }
            if ($request->tanggalperiksa === "01-01-1970") {
                $metadata = array(
                    'message' => 'Tanggal Reservasi Kosong.', // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                
               return  $this->sendErrorNew($metadata,null);
            }

            //cek Rekam Medik berdasarkan NIK
            $dataMedicalrecord = $this->medrecRepository->getMedrecbyNIK($request->nik);
            if ($this->medrecRepository->getMedrecbyNIK($request->nik)->count() < 1 ) {
                $metadata = array(
                    'message' => 'Data pasien ini tidak ditemukan di SIMRS, silahkan Melakukan Registrasi Pasien Baru.',  // Set array status dengan success    
                    'code' => 202, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorNew($metadata,null);
            }else{
               $dtMr = $dataMedicalrecord->first();
               $NamaPasien = $dtMr->PatientName;
               $TglLahir = $dtMr->Date_of_birth;
               $JnsKelamin = $dtMr->Gander;
               $StatusNikahPasien = $dtMr->Marital_status;
               $Alamat = $dtMr->Address;
               $txEmail = $dtMr->Email;
               $NoTlp = $dtMr->tlp;
               $NoHp = $dtMr->Hp;
               $NoHp = $dtMr->Hp;
               $norm = $dtMr->NoMR; 
            }
            //CONVERT No. Mr
            $NoMrfix =   $norm;
            if ($norm <> "") { //  JIKA ADA NO. MEDICAL RECORD
                $MrExist = "1";
            } else {
                $MrExist = "0";
            }
            //cek data dokter udh maping bpjs belum 
            ////return  $IdDokter . ' - ' .$IdGrupPerawatan . ' - ' .$request->jampraktek . ' - ' .$groupjadwal . '  -  ' .(string)$request->kodedokter;

            $dataDoctorbpjs = $this->doctorRepository->getDoctorbyIDBPJS((string)$request->kodedokter);
            if ( $dataDoctorbpjs->count() < 1 ) {
                $response = array(
                    'message' => 'Data ID Dokter BPJS Belum di Maping dalam SIMRS.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'Data ID Dokter BPJS Belum di Maping dalam SIMRS.', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }else{
               $dtdr = $dataDoctorbpjs->first();
               $IdDokter = $dtdr->ID;
               $CodeAntrian = $dtdr->CodeAntrian;
               $NamaDokter = $dtdr->First_Name; 
            }

            //cek Poli nya ada gak
            $dataunitbpjs = $this->unitRepository->getUnitByIdBPJS($request->kodepoli);
            if ( $dataunitbpjs->count() < 1 ) {
                $response = array(
                    'message' => 'Data ID Poliklinik BPJS Belum di Maping dalam SIMRS.' . $dataunitbpjs->count(), // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'Data ID Poliklinik BPJS Belum di Maping dalam SIMRS.' . $dataunitbpjs->count(), // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }else{
               $dtdr = $dataunitbpjs->first();
               $IdGrupPerawatan = $dtdr->ID;
               $NamaGrupPerawatan = $dtdr->NamaUnit; 
            }

            // cek udah pernah booking belum
            if ($NoMrfix <> "") {
                $datanow = $this->appointmenRepository->getBookingCurrentTIme($tglbookingfix,$IdGrupPerawatan,$IdDokter,$NoMrfix);
                $dtnboking = $datanow->first();
   
                if ( $datanow->count() > 0 ) {
                    
                    $response = array(
                        'message' => 'Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal Yang Sama. No. Antrian anda : ' . $dtnboking->NoBooking, // Set array status dengan success     
                    );
                    $metadata = array(
                        'message' => 'Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal Yang Sama. No. Antrian anda : ' . $dtnboking->NoBooking, // Set array status dengan success      
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    );
                    return  $this->sendErrorTrsNew($response,$metadata);
                }
            }
            //cek dokternya cuti engga
            $dtCuti = $this->scheduleRepository->getCutiDokter($IdDokter,$tglbookingfix);
            $dtnboking = $dtCuti->first();
            if ( $dtCuti->count() > 0 ) {
                
                $response = array(
                    'message' => 'Dokter Yang Anda Pilih sedang Cuti.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'Dokter Yang Anda Pilih sedang Cuti.',  // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }

            // group jadwal BPJS
            $groupjadwal="1";

            if($datename == "Sunday"){
               $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSMinggu($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Monday") {
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSSenin($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Tuesday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSSelasa($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Wednesday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSRabu($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Thursday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSKamis($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Friday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSJumat($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Saturday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSSabtu($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            }
            if($jadwal->count() < 1 ){ 
                $response = array(
                    'message' => 'Jam Praktek Tidak Ditemukan di SIMRS, hubungi IT RS Yarsi untuk memperbaiki Jadwal Dokter.',  // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'Jam Praktek Tidak Ditemukan di SIMRS, hubungi IT RS Yarsi untuk memperbaiki Jadwal Dokter.', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }else{
                    $single = $jadwal->first(); 
                    $NamaSesion = $single->NamaSesion;
                    $MaxKuota = $single->MaxKuota;
                    $Max_JKN = $single->Max_JKN;
                    $Max_NonJKN = $single->Max_NonJKN;
                    $JamAwal = $single->JamAwal;
                    $JamAkhir = $single->JamAkhir;
                    $ID_JadwalPraktek = $single->ID;
                    $xesti = $request->tanggalperiksa . ' ' . $JamAwal;
                    date_default_timezone_set("Asia/Jakarta");
                    $estimasi2 = strtotime($xesti);
                    $estimasi = $estimasi2*1000;
            }
            $dt = Carbon::now()->toTimeString();
            $waktureal = date("H:i",strtotime($dt));
            $waktupoliakhir = date("H:i", strtotime($JamAkhir));
         
            // if($waktureal > $waktupoliakhir){
            //     $metadata = array(
            //         'message' => 'failed',
            //         'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            //     );
            //     $response = array(
            //         'message' => 'Pendaftaran Ke Poli '.$NamaGrupPerawatan.' Sudah Tutup Jam '. $JamAkhir,  // Set array status dengan success     
            //     );
                 
            //     return  $this->sendErrorTrsNew($response,$metadata);
            // }

            // get max id Apointment
            $maxnumber = $this->appointmenRepository->getMaxAppointmentNumber();
            $appMaxNumber = $maxnumber->ID;
            $appMaxNumber++;

            //get max number antrian  
            $dataAntrian = $this->genNumberAntrianPoliklinik($tglbookingfix,$NamaSesion,$IdDokter,$CodeAntrian);
            $fixNoAntrian = $dataAntrian[1];
            $idno_urutantrian = $dataAntrian[0];

             // Generate No Booking
             $Notrsbooking = $this->genBookingNumber($tglbookingfix,$idbookingres);
             $xres = $idbookingres . '-' . $Notrsbooking[0];
             $nouruttrx = $Notrsbooking[0];
             $nobokingreal = $Notrsbooking[1];
             $ID_Penjamin = '313';
             
            // START - cek sisa kuota
            $harindo =  ""; 
            if($datename == "Sunday"){
                $harindo = "Minggu";
                $kuotaPoliklinik = $this->antrianRepository->AntrianPoliklinikByDoctorPoliMinggu($tglbookingfix,$IdDokter,$IdGrupPerawatan,$request->jampraktek);
            } elseif ($datename == "Monday") {
                $harindo = "Senin";
                $kuotaPoliklinik = $this->antrianRepository->AntrianPoliklinikByDoctorPoliSenin($tglbookingfix,$IdDokter,$IdGrupPerawatan,$request->jampraktek);
            } elseif ($datename == "Tuesday") { 
                $harindo = "Selasa";
                $kuotaPoliklinik = $this->antrianRepository->AntrianPoliklinikByDoctorPoliSelasa($tglbookingfix,$IdDokter,$IdGrupPerawatan,$request->jampraktek);
            } elseif ($datename == "Wednesday") { 
                $harindo = "Rabu";
                $kuotaPoliklinik = $this->antrianRepository->AntrianPoliklinikByDoctorPoliRabu($tglbookingfix,$IdDokter,$IdGrupPerawatan,$request->jampraktek);
            } elseif ($datename == "Thursday") { 
                $harindo = "Kamis";
                $kuotaPoliklinik = $this->antrianRepository->AntrianPoliklinikByDoctorPoliKamis($tglbookingfix,$IdDokter,$IdGrupPerawatan,$request->jampraktek);
            } elseif ($datename == "Friday") {
                $harindo = "Jumat"; 
                $kuotaPoliklinik = $this->antrianRepository->AntrianPoliklinikByDoctorPoliJumat($tglbookingfix,$IdDokter,$IdGrupPerawatan,$request->jampraktek);
            } elseif ($datename == "Saturday") { 
                $harindo = "Sabtu";
                $kuotaPoliklinik = $this->antrianRepository->AntrianPoliklinikByDoctorPoliSabtu($tglbookingfix,$IdDokter,$IdGrupPerawatan,$request->jampraktek);
            }
            
            $koutaPerPoli = $kuotaPoliklinik->count();
            $Ant = $koutaPerPoli+1;
            if($request->groupjadwal=="2"){
                if($koutaPerPoli >= $Max_NonJKN){
                    //  Sudah Tutup Registrasi. 
                    // return $this->sendError("Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ." Sudah Penuh, Kuota Maksimal ". $Max_NonJKN . ", No. Antrian Anda Adalah : " .  $Ant . ". Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.", []);  
                     $metadata = array(
                        'message' => "Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ." Sudah Tutup Registrasi. Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.",
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    ); 
                    return  $this->sendErrorNew($metadata,null);
               
                }
            }else{
                if($koutaPerPoli >= $Max_JKN){
                    // return $this->sendError("Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ." Sudah Penuh, Kuota Maksimal ". $Max_JKN . ", No. Antrian Anda Adalah : " .  $Ant . ". Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.", []);  
                     $metadata = array(
                        'message' => "Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ." Sudah Tutup Registrasi. Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.",
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    ); 
                    return  $this->sendErrorNew($metadata,null);
                }
            }
            // END - cek sisa kuota

             // INSERT TABEL BOOKING
             $this->appointmenRepository->AmbilAntrian($request,$JenisBoking,$idbooking,$nouruttrx,$TglLahir,$JnsKelamin,
             $StatusNikahPasien,$IdGrupPerawatan,$NamaGrupPerawatan,$IdDokter,
             $NamaDokter,$NamaSesion,$idno_urutantrian,
             $fixNoAntrian,$NamaPasien,$tglbookingfix,$nobokingreal,
             $xres,$MrExist,$Company,$kodejenispayment,$NoTlp,$NoHp,$Alamat,$datenowcreate,
             $noteall,$txEmail,$NoMrfix,$ID_Penjamin,$ID_JadwalPraktek,'','');

             // INSERT TABEL ANTRIAN
             $this->antrianRepository->insertAntrian($nobokingreal,$IdDokter,$NamaSesion,$idno_urutantrian,$fixNoAntrian,$tglbookingfix,$Company);
             DB::commit();
             $response = array(
                'nomorantrean' => $fixNoAntrian,
                'angkaantrean' => $idno_urutantrian,
                'kodebooking' => $nobokingreal,
                'norm' => $norm,
                'namapoli' => $NamaGrupPerawatan,
                'namadokter' => $NamaDokter,
                'estimasidilayani' => $estimasi,
                'sisakuotajkn' => 0,
                'kuotajkn' => $Max_JKN,
                'sisakuotanonjkn' => 0,
                'kuotanonjkn' => $Max_NonJKN,
                'keterangan' => "Peserta harap 60 menit lebih awal guna pencatatan administrasi.", 
            );
            if($MrExist=="1"){
                $metadata = array(
                    'message' => 'Ok',
                    'code' => 200,
                );
            }else{
                $metadata = array(
                    'message' => 'Pasien Baru',
                    'code' => 202,
                );
            }
            return $this->sendResponseNew($response, $metadata); 
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => $e->getMessage(), // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }
    }  
    public function SisaStatusAntrian(Request $request){
        try {
            $kodebooking = $request->kodebooking;
            $nobooking = $this->appointmenRepository->ViewBookingbyId($kodebooking);
            if($nobooking->count() > 0){
                $datano = $nobooking->first();
                $DoctorID = $datano->DoctorID;
                $JamPraktek = $datano->JamPraktek;
                $ApmDate = $datano->ApmDate;
                $NamaDokter = $datano->NamaDokter;
                $Poli = $datano->Poli;
                $NoAntrianAll = $datano->NoAntrianAll;
                $JamPraktek = $datano->JamPraktek;
            }else{
                $metadata = array(
                    'message' => 'Antrean Tidak Ditemukan.',
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                $response = array(
                    'message' => 'Antrean Tidak Ditemukan.',  // Set array status dengan success     
                );
                 
                return  $this->sendErrorTrsNew($response,$metadata);
            }

            // Cari Total Antrian Hari tertentu atas dokter dan jam perakter tertentu
            $getDataAntrianAll = $this->antrianRepository->getAntrianPoliByDateDoctor($JamPraktek,$JamPraktek,$DoctorID)->first();
            $dataAntrianAll = $getDataAntrianAll->BlmPanggil;

            //Cari Total Antrian Hari tertentu yang sudah di panggil atas dokter dan jam perakter tertentu
            $getdataAntrianCalled = $this->antrianRepository->getAntrianPoliCalledByDateDoctor($JamPraktek,$JamPraktek,$DoctorID)->first();
            $dataAntrianCalled = $getdataAntrianCalled->SdhPanggil;

            // Cari Antrian CUrrent
            $getdataAntrianCurrent = $this->antrianRepository->getAntrianPoliCurrentByDateDoctor($JamPraktek,$JamPraktek,$DoctorID);
            if($getdataAntrianCurrent){
                $CurrentCall = $getdataAntrianCurrent->noAntrianAll;
            }else{
                $CurrentCall = 0;
            }
            $sisaantrean = $dataAntrianAll - $dataAntrianCalled;
            // $dataAntrianCalled = $getdataAntrianCurrent->SdhPanggil;
            $response = array(
                    'nomorantrean' => $NoAntrianAll, // Set array status dengan success     
                    'namapoli' => $Poli, // Set array status dengan success     
                    'namadokter' => $NamaDokter, // Set array status dengan success     
                    'sisaantrean' => $sisaantrean, // Set array status dengan success     
                    'antreanpanggil' => $CurrentCall, // Set array status dengan success     
                    'waktutunggu' => "", // Set array status dengan success     
                    'keterangan' => "", // Set array status dengan success     
            );
            $metadata = array(
                'message' => 'Ok', // Set array status dengan success    
                'code' => 200, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return $this->sendResponseNew($response, $metadata);  
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => $e->getMessage(), // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }
        

    }  
    public function StatusAntrian(Request $request){
        try {
            $kodepoli = $request->kodepoli;
            $kodedokter = (string)$request->kodedokter;
            $tanggalperiksa = $request->tanggalperiksa;
            $datename = date("l", strtotime(trim(strip_tags( $request->tanggalperiksa))));
            $jampraktek = $request->jampraktek;
            $groupjadwal ="1";
            $datenow = Carbon::now()->toDateString();
        

            if ($kodepoli === "" || $kodepoli === null) {
                $metadata = array(
                    'message' => "Kode Poli Tidak Ditemukan.", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($kodedokter === "" || $kodedokter === null) {
                $metadata = array(
                    'message' => 'Kode Dokter Tidak Ditemukan.', // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if ($tanggalperiksa === "01-01-1970") {
                $metadata = array(
                    'message' => 'Tanggal Tidak Ditemukan / Kosong.', // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return  $this->sendErrorNew($metadata,null);
            }
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tanggalperiksa)) {
                $metadata = array(
                    'message' => "Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorNew($metadata,null);
            }
            if (date("Y-m-d",strtotime($tanggalperiksa)) <  date("Y-m-d",strtotime($datenow))) {
                $metadata = array(
                    'message' => 'Tanggal Periksa Tidak Berlaku.', // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorNew($metadata,null);
            }
            if ($jampraktek === "" || $jampraktek === null) {
                $metadata = array(
                    'message' => 'Jam Praktek Tidak Ditemukan.', // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorNew($metadata,null);
            }

            //cek data dokter udh maping bpjs belum
            $dataDoctorbpjs = $this->doctorRepository->getDoctorbyIDBPJS((string)$request->kodedokter);
            if ( $dataDoctorbpjs->count() < 1 ) {
                $response = array(
                    'message' => 'Data ID Dokter BPJS Belum di Maping dalam SIMRS.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }else{
            $dtdr = $dataDoctorbpjs->first();
            $IdDokter = $dtdr->ID;
            $CodeAntrian = $dtdr->CodeAntrian;
            $NamaDokter = $dtdr->First_Name; 
            }

            //cek Poli nya ada gak
            $dataunitbpjs = $this->unitRepository->getUnitByIdBPJS($request->kodepoli);
            if ( $dataunitbpjs->count() < 1 ) {
                $response = array(
                    'message' => 'Data ID Poliklinik BPJS Belum di Maping dalam SIMRS.' . $dataunitbpjs->count(), // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }else{
            $dtdr = $dataunitbpjs->first();
            $IdGrupPerawatan = $dtdr->ID;
            $NamaGrupPerawatan = $dtdr->NamaUnit; 
            }

            // validasi jam praktek
            if($datename == "Sunday"){
            $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSMinggu($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Monday") {
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSSenin($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Tuesday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSSelasa($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Wednesday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSRabu($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Thursday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSKamis($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Friday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSJumat($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            } elseif ($datename == "Saturday") { 
                $jadwal = $this->scheduleRepository->getScheduleDoctorForTRSSabtu($IdDokter,$IdGrupPerawatan,$request->jampraktek,$groupjadwal);
            }
            if($jadwal->count() < 1 ){ 
                $response = array(
                    'message' => 'Jam Praktek Tidak Ditemukan.',  // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }else{
                    $single = $jadwal->first(); 
                    $NamaSesion = $single->NamaSesion;
                    $MaxKuota = $single->MaxKuota;
                    $Max_JKN = $single->Max_JKN;
                    $Max_NonJKN = $single->Max_NonJKN;
                    $JamAwal = $single->JamAwal;
                    $JamAkhir = $single->JamAkhir;
                    $ID_JadwalPraktek = $single->ID;
                    $xesti = $request->tanggalperiksa . ' ' . $JamAwal;
                    date_default_timezone_set("Asia/Jakarta");
                    $estimasi2 = strtotime($xesti);
                    $estimasi = $estimasi2*1000;
            }

            // Cari Total Antrian Hari tertentu atas dokter dan jam perakter tertentu
            $getDataAntrianAll = $this->antrianRepository->getAntrianPoliByDateDoctor($tanggalperiksa,$NamaSesion,$IdDokter)->first();
            $dataAntrianAll = $getDataAntrianAll->BlmPanggil;

            //Cari Total Antrian Hari tertentu yang sudah di panggil atas dokter dan jam perakter tertentu
            $getdataAntrianCalled = $this->antrianRepository->getAntrianPoliCalledByDateDoctor($tanggalperiksa,$NamaSesion,$IdDokter)->first();
            $dataAntrianCalled = $getdataAntrianCalled->SdhPanggil;

            // Cari Antrian CUrrent
            $getdataAntrianCurrent = $this->antrianRepository->getAntrianPoliCurrentByDateDoctor($tanggalperiksa,$NamaSesion,$IdDokter);
            if($getdataAntrianCurrent){
                $CurrentCall = $getdataAntrianCurrent->noAntrianAll;
            }else{
                $CurrentCall = 0;
            }
            $sisaantrean = $dataAntrianAll - $dataAntrianCalled;
            // $dataAntrianCalled = $getdataAntrianCurrent->SdhPanggil;
            $response = array(
                'namapoli' => $NamaGrupPerawatan, // Set array status dengan success     
                'namadokter' => $NamaDokter, // Set array status dengan success     
                'totalantrean' => $dataAntrianAll, // Set array status dengan success     
                'sisaantrean' => $sisaantrean, // Set array status dengan success     
                'antreanpanggil' => $CurrentCall, // Set array status dengan success     
                'sisakuotajkn' =>  $Max_JKN, // Set array status dengan success     
                'kuotajkn' =>  $Max_JKN, // Set array status dengan success     
                'sisakuotanonjkn' =>  $Max_NonJKN, // Set array status dengan success     
                'kuotanonjkn' =>  $Max_NonJKN, // Set array status dengan success     
                'keterangan' => "", // Set array status dengan success       
            );
            $metadata = array(
                'message' => 'Ok', // Set array status dengan success    
                'code' => 200, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return $this->sendResponseNew($response, $metadata); 
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => 'Gagal', // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }
    }  
    public function BatalAntrian(Request $request){

        DB::connection('sqlsrv3')->beginTransaction();

        try{
            
            $kodebooking =  $request->kodebooking;
            $dataBooking = $this->appointmenRepository->ViewBookingbyId($kodebooking);
            if($dataBooking->count() < 1){
                $response = array(
                    'message' => 'Antrian Tidak Ditemukan.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }
                $dataViewBooking = $dataBooking->first();
                $DoctorID = $dataViewBooking->DoctorID;
                $JamPraktek = $dataViewBooking->JamPraktek;
                $ApmDate = $dataViewBooking->ApmDate;
                $NamaDokter = $dataViewBooking->NamaDokter;
                $Poli = $dataViewBooking->Poli;
                $NoAntrianAll = $dataViewBooking->NoAntrianAll;

            $dataAntrian = $this->antrianRepository->getAntrianbyKodeBooking($kodebooking);
            if($dataAntrian->count() < 1){
                $response = array(
                    'message' => 'No. Antrian Tidak Ditemukan.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }

                $dtantrian = $dataAntrian->first();
                $StatusAntrian = $dtantrian->StatusAntrian;
                $no_transaksi = $dtantrian->no_transaksi;
                $noAntrianAll = $dtantrian->noAntrianAll;
                $batal = $dtantrian->batal;

                if ($StatusAntrian > 0) {
                    $response = array(
                        'message' => 'Pasien Sudah Dilayani, Antrean Tidak Dapat Dibatalkan.', // Set array status dengan success     
                    );
                    $metadata = array(
                        'message' => 'failed', // Set array status dengan success    
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    );
                    return  $this->sendErrorTrsNew($response,$metadata); 
                }
                if ($batal > 0) {

                    $response = array(
                        'message' => 'Antrean Tidak Ditemukan atau Sudah Dibatalkan.', // Set array status dengan success     
                    );
                    $metadata = array(
                        'message' => 'failed', // Set array status dengan success    
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    );
                    return  $this->sendErrorTrsNew($response,$metadata); 
                }

                // Batal Transaksi Booking
                $this->appointmenRepository->BatalAntrian($kodebooking);
                // Batal Transaksi Antrian
                $this->antrianRepository->voidAntrian($kodebooking);

                DB::connection('sqlsrv3')->commit();
                $response = array(
                    'message' => 'Antrian Berhasil Dihapus.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'Ok', // Set array status dengan success    
                    'code' => 200, // Set array nama dengan isi kolom nama pada tabel siswa 
                ); 
                return $this->sendResponseNew($response, $metadata); 
                
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => 'Gagal', // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }  
    }  
    public function CheckIn(Request $request){ 
        DB::connection('sqlsrv3')->beginTransaction();
        try{
            
            $kodebooking = $request->kodebooking; 
            $waktu = $request->waktu;
            $getBooking = $this->appointmenRepository->ViewBookingbyId($kodebooking);
            if($getBooking->count() < 1){
                $response = array(
                    'message' => 'No. Antrian Tidak Ditemukan.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'No. Antrian Tidak Ditemukan.', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata);
            }
                $databooking = $getBooking->first();
                
                $batal = $databooking->batal;
                $Datang = $databooking->Datang;
                $NoAntrianAll = $databooking->NoAntrianAll;
                $Antrian = $databooking->Antrian;
                $NoMrfix = $databooking->NoMR;
                $NoMr = str_replace("-", "", $NoMrfix);
                $ApmDate = $databooking->ApmDate;
                $JenisBayar = "5";
                $shift = $databooking->JamPraktek;
                $IdDokter = $databooking->DoctorID;
                $NamaGrupPerawatan = $databooking->Poli;
                $IdGrupPerawatan = $databooking->IdPoli;
                $Company = $databooking->Company;
                $ID_JadwalPraktek = $databooking->ID_JadwalPraktek;

                if($Datang > 0){
                    $response = array(
                        'message' => 'Antrean Sudah Checkin, No. Booking tidak berlaku.', // Set array status dengan success     
                    );
                    $metadata = array(
                        'message' => 'Antrean Sudah Checkin, No. Booking tidak berlaku.',  // Set array status dengan success    
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    );
                    return  $this->sendErrorTrsNew($response,$metadata);
                }

                // validasi checkin, tapi DATA PROVINSI KOSONG
                $dataMedrecProvinsi = $this->medrecRepository->getMedrecbyNoMR($NoMrfix);
                $xdataMedrecProvinsi = $dataMedrecProvinsi->first();
               
                if ($xdataMedrecProvinsi->kelurahan == null ) {
                    $metadata = array(
                        'message' => 'Data Medical Record anda belum lengkap, Silahkan ke Counter Pendaftaran untuk melengkapi Data Medical Record Anda.',  // Set array status dengan success    
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    );
                    return  $this->sendErrorNew($metadata,null);
                }
                
            //get max visit
            $maxVisit = $this->visitRepository->getMaxnumberVisit();
            $maxVisit->ID++;
            $kodeRegAwalXX = "RJJP";
            $operator = "2852"; 
            $CaraBayar = "5";
            $Perusahaan = "313";
            $idCaraMasuk = "1";
            $idAdmin = "5";
            $TelemedicineIs="0";
            $Tipe_Registrasi = "1";

                $datenow2 = date('Y-m-d', strtotime($ApmDate));
                $datenowcreate = $datenow2;
                $datenow = date('dmy', strtotime($datenowcreate));


            // get last registration active
            $lastreg = $this->visitRepository->getRegistrationActivebyMedrec($NoMrfix);
            if($lastreg){
                $NoEpisode = $lastreg->NoEpisode;
                

                // Generate No Registrasi terbaru
                $NoregistrationRajal = $this->genNumberRegistrationRajal($datenowcreate,$kodeRegAwalXX,$datenow,$NoMrfix);
                $nofixReg = $NoregistrationRajal[1];

                // cek apakah sudah daftar belum
                $cekActiverRegistrasi = $this->visitRepository->getActiveRegistrationToday($NoMrfix,$IdGrupPerawatan,
                                        $IdDokter,$shift,$ApmDate);

                if($cekActiverRegistrasi){
                    $response = array(
                        'message' => 'Pasien Sudah mendaftar di Poli dan Dokter yang sama !', // Set array status dengan success     
                    );
                    $metadata = array(
                        'message' => 'Pasien Sudah mendaftar di Poli dan Dokter yang sama !',// Set array status dengan success    
                        'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                    );
                    return  $this->sendErrorTrsNew($response,$metadata);
                } 
              

            }else{ // JIKA TIDAK ADA REG AKTIF
                $NoregistrationRajal = $this->genNumberRegistrationRajal($datenowcreate,$kodeRegAwalXX,$datenow,$NoMrfix);
                $NoEpisode = $NoregistrationRajal[3];
                $auto_eps = $NoregistrationRajal[4];
                $id_eps = $NoregistrationRajal[5];
                $nofixReg = $NoregistrationRajal[1];

            
            }
            $catatan = '';
            $FisioterapiFlag = '0';
            if($IdGrupPerawatan == "17"){
                $FisioterapiFlag = '1';
            }else{
                $FisioterapiFlag = '0';
            }
                // // INSERT REGISTRATION
                $this->visitRepository->addRegistrationRajal($maxVisit->ID,$NoEpisode,$nofixReg,$NamaGrupPerawatan,$NoMrfix,
                                                            $JenisBayar,$IdGrupPerawatan,$IdDokter,$Antrian,$NoAntrianAll,
                                                            $Company,$shift,$TelemedicineIs,$ApmDate,
                                                            $ApmDate,$operator,$CaraBayar,$Perusahaan,$idCaraMasuk,
                                                            $idAdmin,$Tipe_Registrasi,$ID_JadwalPraktek,$catatan,$FisioterapiFlag);

                // //  UPDATE DATANG RESERVASI
                $this->appointmenRepository->updateDatangAppointment($kodebooking,$nofixReg); 

                // INSERT KE DASHBAORD

                
                // DATA 
                $jenispasien = "JKN";
                
                // SEND DATA KE TASK ID
                $taskid = "1";
                $conId = "13384";
                $secId = "4eA130B116";
                $userkey = "2ddc17e0903dfe63e07bf37d602106d6";
                date_default_timezone_set('UTC');
                $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
                $signature = base64_encode(hash_hmac('sha256', $conId . "&" . $tStamp, $secId, true));

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL =>  'https://apijkn.bpjs-kesehatan.go.id/antreanrs/antrean/updatewaktu',
                    // CURLOPT_URL =>  'https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev/antrean/updatewaktu',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '                                                
                        {
                            "kodebooking": "' . $kodebooking . '",
                            "taskid":  "' . $taskid . '",
                            "waktu": "' . $waktu . '"
                        }',
                    CURLOPT_HTTPHEADER => array(
                        'x-cons-id: ' . $conId,
                        'x-timestamp: ' . $tStamp,
                        'x-signature: ' . $signature,
                        'user_key: ' . $userkey,
                        'Content-Type: application/json'
                    ),
                ));
                $outputx = curl_exec($curl);
                // tutup curl 
                curl_close($curl); 
                $waktu2=$waktu/1000;
                $tgl_input =  date('Y-m-d H:i:s', $waktu2);
                // // ubah string JSON menjadi array
                $JsonData = json_decode($outputx, TRUE);  
                       if ($JsonData['metadata']['code'] == "200") {
                            $this->visitRepository->addTaskOneBPJS($kodebooking,$waktu,$taskid,$tgl_input);
                            DB::connection('sqlsrv3')->commit();
                            $response = array(
                                'message' => 'Checkin SIMRS Berhasil.', // Set array status dengan success     
                            );
                            $metadata = array(
                                'message' => 'Ok', // Set array status dengan success    
                                'code' => 200, // Set array nama dengan isi kolom nama pada tabel siswa 
                            ); 
                            return $this->sendResponseNew($response, $metadata);
                       } else if ($JsonData['metadata']['code'] == "201") {  
                            DB::connection('sqlsrv3')->rollBack();
                            $response = array(
                                'message' => 'Kode Booking Tidak Ditemukan di Mobile JKN.', // Set array status dengan success     
                            );
                            $metadata = array(
                                'message' => "Kode Booking Tidak Ditemukan di Mobile JKN.", // Set array status dengan success    
                                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                            ); 
                            return $this->sendResponseNew($JsonData, $metadata);
                        }     
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => $e->getMessage(), // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }   
    }  
    public function UpdateTaskID(Request $request){
      
    }  
    public function ViewBookingbyId(Request $request){
      try{
            $data = $this->appointmenRepository->ViewBookingbyId($request->kodebooking);
            $datafirst=$data->first();
            if($data->count() < 1){
                $response = array(
                    'message' => 'Kode Booking Tidak Ditemukan.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorTrsNew($response,$metadata); 
            }
            $response = array(
                'DoctorID' => $datafirst->DoctorID, // Set array status dengan success     
                'namapoli' => $datafirst->Poli, // Set array status dengan success     
                'namadokter' => $datafirst->NamaDokter, // Set array status dengan success     
                'JamPraktek' => $datafirst->JamPraktek, // Set array status dengan success     
                'ApmDate' => $datafirst->ApmDate, // Set array status dengan success     
                'NoAntrianAll' => $datafirst->NoAntrianAll, // Set array status dengan success     
                'NoBooking' => $datafirst->NoBooking, // Set array status dengan success     
                'NamaPasien' => $datafirst->NamaPasien, // Set array status dengan success     
            );
            $metadata = array(
                'message' => 'Ok', // Set array status dengan success    
                'code' => 200, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return $this->sendResponseNew($response, $metadata); 
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            Log::info($e->getMessage());
            $metadata = array(
                'message' => 'Gagal', // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }  
    }  
     
     
}
