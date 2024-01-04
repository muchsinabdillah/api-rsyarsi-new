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
date_default_timezone_set("Asia/Jakarta");
class bAppointmenNonBPJSService extends Controller {
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

    public function CreateAppointment(Request $request){
        try{
            
            DB::beginTransaction();
            // persiapan Insert
            $noRujukan  = "";
            $JenisBayar=$request->JenisBayar;
            $Company=$request->Company;
            $JenisBoking =$request->JenisBoking;
            $datenowcreate = Carbon::now();
            $ID_Penjamin = $request->ID_Penjamin;
            $txNamaPenjamin = $request->Nama_Penjamin;
            $noteall = $txNamaPenjamin . " - " . $Company;
            $Userid_Mobile = $request->Userid_Mobile;
            $noRujukan = $request->noRujukanBpjs;

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
                return $this->sendError('Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd', []);
            }
            //cek kelengkapan data
              
            if ($request->nohp === "" || $request->nohp === null ) { 
                return $this->sendError("No. Handphone Kosong.", []);
            }

            if ($request->kodedokter === "" || $request->kodedokter === null) { 
                return $this->sendError("Kode Dokter Kosong.", []);
            }
            if ($request->kodepoli === "" || $request->kodepoli === null) { 
               return $this->sendError("Kode Poliklinik Kosong.", []);
            }
            if ($request->jampraktek === "" || $request->jampraktek === null) { 
               return $this->sendError("Jam Praktek Kosong.", []);
            }
            if ($request->tanggalperiksa === "01-01-1970") { 
               return $this->sendError("Tanggal Reservasi Kosong.", []);
            }
            if ($request->tanggalperiksa === "1970-01-01") { 
               return $this->sendError("Tanggal Reservasi Kosong.", []);
            }

            //cek Rekam Medik berdasarkan NIK
            $dataMedicalrecord = $this->medrecRepository->getMedrecbyNoMR($request->NoMr);
      
            if ($this->medrecRepository->getMedrecbyNoMR($request->NoMr)->count() < 1 ) { 
                $NamaPasien = $request->NamaPasien;
                $TglLahir ="";
                $JnsKelamin = "";
                $StatusNikahPasien = "";
                $Alamat = "";
                $txEmail = "";
                $NoTlp =$request->nohp;
                $NoHp = $request->nohp;
                $norm = $request->NoMr; 
                $MrExist = "0";
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
               $MrExist = "1";
            }
            $NoMrfix = $norm;
            
            //cek data dokter
            $dataDoctorbpjs = $this->doctorRepository->getDoctorbyId($request->kodedokter);
            if ( $dataDoctorbpjs->count() < 1 ) { 
                return $this->sendError("Data ID Dokter Tidak Ditemukan / Invalid.", []); 
            }else{
               $dtdr = $dataDoctorbpjs->first();
               $IdDokter = $dtdr->ID;
               $CodeAntrian = $dtdr->CodeAntrian;
               $NamaDokter = $dtdr->NamaDokter; 
            }

            //cek Poli nya ada gak
            $dataunitbpjs = $this->unitRepository->getUnitById($request->kodepoli);
            if ( $dataunitbpjs->count() < 1 ) {
                return $this->sendError("Data ID Poliklinik Tidak Ditemukan / Invalid.", []);  
            }else{
               $dtdr = $dataunitbpjs->first();
               $IdGrupPerawatan = $dtdr->ID;
               $NamaGrupPerawatan = $dtdr->NamaUnit; 
            }

            

            //cek dokternya cuti engga
            $dtCuti = $this->scheduleRepository->getCutiDokter($IdDokter,$tglbookingfix); 
            if ( $dtCuti->count() > 0 ) { 
                return $this->sendError("Dokter Yang Anda Pilih sedang Cuti.", []);  
            }

            // group jadwal BPJS
            $groupjadwal=$request->groupjadwal;

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
                return $this->sendError("Jam Praktek Tidak Ditemukan.", []);  
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
            $dmyreal =  date("Y-m-d",strtotime($dt));
            $waktureal = date("H:i",strtotime($dt));
            $waktupoliakhir = date("H:i", strtotime($JamAkhir));
            $Fixwaktureal = date("Y-m-d H:i",strtotime($tglbookingfix.' '. $waktureal));
            $Fixwaktupoliakhir = date("Y-m-d H:i",strtotime($tglbookingfix.' '. $waktupoliakhir));
            // cari selisih
            $createdApp = new Carbon($tglbookingfix);
            $now = Carbon::now();
            $difference = $createdApp->diff($now)->days;
            //Booking di bawah tgl skrg g bisa
            if($tglbookingfix < $dmyreal){
                return $this->sendError("Tanggal Booking : ".$tglbookingfix." Lebih Kecil dari tanggal Hari ini : ".$dmyreal, []);  
            }
            // Booking Harus harus H+1
            // if($tglbookingfix == $dmyreal){
            //     return $this->sendError("Appointment hanya bisa dilakukan H-1 dari Hari ini.", []);  
            // }
            // Poli Sudah Tutup
            // if($Fixwaktureal > $Fixwaktupoliakhir){ 
            //     return $this->sendError("Pendaftaran Ke Poli ".$NamaGrupPerawatan." Sudah Tutup Jam ". $JamAkhir
            //                     . "Waktu Booking : ".$Fixwaktureal." Waktu Akhir Poli : ". $Fixwaktupoliakhir, []);  
            // }
            if($difference > 30){
                return $this->sendError("Appointment dilakukan maksimal 30 hari.", []);
            }
             
            // cek udah pernah booking belum
            if ($NoMrfix <> "" && $NoMrfix <> "-") {
                $datanow = $this->appointmenRepository->getBookingCurrentTIme($tglbookingfix,$IdGrupPerawatan,$IdDokter,$NoMrfix);
                $dtnboking = $datanow->first();
                if ( $datanow->count() > 0 ) { 
                    return $this->sendError('Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal Yang Sama. No. Reservasi anda : ' . $dtnboking->NoBooking. " - " .$NoMrfix, []);  
                }
            }

            // cek jika medrec - dari website atau mobile rs yarsi
            if ($NoMrfix == "-") {
                $datanow = $this->appointmenRepository->getBookingCurrentTImeWeb($tglbookingfix,$IdGrupPerawatan,$IdDokter,$NoMrfix,$NoHp);
                $dtnboking = $datanow->first();
                if ( $datanow->count() > 0 ) { 
                    return $this->sendError('Data Medrec Anda belum terverifikasi, Anda cuma Bisa Melakukan Reservasi hanya 1 kali. Silahkan Verifikasi Medical Record Anda Ke Petugas Admission Kami.', []);  
                }
            }
            
            // get max id Apointment
            $maxnumber = $this->appointmenRepository->getMaxAppointmentNumber();
            $appMaxNumber = $maxnumber->ID;
            $appMaxNumber++;

            //get max number antrian  
            $dataAntrian = $this->genNumberAntrianPoliklinik($tglbookingfix,$NamaSesion,$IdDokter,$CodeAntrian);
            $fixNoAntrian = $dataAntrian[1];
            $idno_urutantrian = $dataAntrian[0];
           
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
                    // return $this->sendError("Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ."  Sudah Penuh, Kuota Maksimal ". $Max_NonJKN  . ", No. Antrian Anda Adalah : " .  $Ant . ". Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.", []);  
                    return $this->sendError("Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ."  Sudah Tutup Registrasi. Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.", []);  
                }
            }else{
                if($koutaPerPoli >= $Max_JKN){
                    // return $this->sendError("Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ." Sudah Penuh, Kuota Maksimal ". $Max_JKN . ", No. Antrian Anda Adalah : " .  $Ant . ". Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.", []);  
                    return $this->sendError("Kuota Dokter : " . $NamaDokter . ", Hari : " .$harindo ." Sudah Tutup Registrasi. Silahkan Pilih tanggal Lain untuk Melakukan Booking/Reservasi kembali.", []);  
                }
            }
            // END - cek sisa kuota

             // Generate No Booking
             $Notrsbooking = $this->genBookingNumber($tglbookingfix,$idbookingres);
             $xres = $idbookingres . '-' . $Notrsbooking[0];
             $nouruttrx = $Notrsbooking[0];
             $nobokingreal = $Notrsbooking[1];
             
             
             // INSERT TABEL BOOKING
             $this->appointmenRepository->AmbilAntrian($request,$JenisBoking,$idbooking,$nouruttrx,$TglLahir,$JnsKelamin,
             $StatusNikahPasien,$IdGrupPerawatan,$NamaGrupPerawatan,$IdDokter,
             $NamaDokter,$NamaSesion,$idno_urutantrian,
             $fixNoAntrian,$NamaPasien,$tglbookingfix,$nobokingreal,
             $xres,$MrExist,$Company,$kodejenispayment,$NoTlp,$NoHp,$Alamat,$datenowcreate,
             $noteall,$txEmail,$NoMrfix,$ID_Penjamin,$ID_JadwalPraktek,$Userid_Mobile,$noRujukan);

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
                'keterangan' => "Peserta harap 60 menit lebih awal guna pencatatan administrasi.", 
            ); 
            return $this->sendResponse( $response,"Reservasi Berhasil Di Buat.");  
        }catch (Exception $e) { 
            DB::rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);  
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
                    'message' => 'failed',
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
                'message' => 'Gagal', // Set array status dengan success     
                'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            return  $this->sendErrorTrsNew($e->getMessage(),$metadata);
        }
        

    }  
    public function StatusAntrian(Request $request){
        try {
            $kodepoli = $request->kodepoli;
            $kodedokter = $request->kodedokter;
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
            $dataDoctorbpjs = $this->doctorRepository->getDoctorbyIDBPJS($request->kodedokter);
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
    public function voidAppoitment(Request $request){

        DB::connection('sqlsrv3')->beginTransaction();

        try{
            
            $kodebooking =  $request->kodebooking;
            $keterangan =  $request->keterangan;

            $dataBooking = $this->appointmenRepository->ViewBookingbyId($kodebooking);
            if($dataBooking->count() < 1){ 
                return $this->sendError("No. Reservasi Tidak Ditemukan.", []);
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
                return $this->sendError("No. Reservasi Tidak Ditemukan.", []);
            }

                $dtantrian = $dataAntrian->first();
                $StatusAntrian = $dtantrian->StatusAntrian;
                $no_transaksi = $dtantrian->no_transaksi;
                $noAntrianAll = $dtantrian->noAntrianAll;
                $batal = $dtantrian->batal;

                if ($StatusAntrian > 0) { 
                    return $this->sendError("Pasien Sudah melakukan Checkin, Reservasi Tidak Dapat Dibatalkan.", []);
                }
                if ($batal > 0) { 
                    return $this->sendError("No. Reservasi Tidak Ditemukan atau Sudah Dibatalkan.", []);
                }

                // Batal Transaksi Booking
                $this->appointmenRepository->BatalAntrian($kodebooking);
                // Batal Transaksi Antrian
                $this->antrianRepository->voidAntrian($kodebooking);

                DB::connection('sqlsrv3')->commit();
             
                return $this->sendResponse( [],"No. Reservasi Berhasil Di Hapus.");  
                
        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []);
        }  
    }  
    public function CheckMedrecCheckIn(Request $request){ 
       
        try{ 
            DB::connection('sqlsrv3')->beginTransaction();

            $kodebooking = $request->kodebooking; 
            $NoMR = $request->NoMR;
            $getBooking = $this->appointmenRepository->ViewBookingbyId($kodebooking); 
            if($getBooking->count() < 1){
                $response = array(
                    'message' => 'No. Reservasi Tidak Ditemukan.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return $this->sendError("No. Reservasi Tidak Ditemukan atau Sudah Dibatalkan.", []); 
            }else{ 
                 $this->appointmenRepository->updateNoMrAppointment($kodebooking,$NoMR);
                 DB::connection('sqlsrv3')->commit();
                 return $this->sendResponse([] ,"No. Medical Record  Berhasil Di Update di data Apointment.");   
            }
        } catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []); 
        }  
    }
    public function CheckIn(Request $request){ 
        DB::connection('sqlsrv3')->beginTransaction();
        try{ 
            
            $kodebooking = $request->kodebooking; 
            $Company = $request->Company; 
            $waktu = $request->waktu;
            $getBooking = $this->appointmenRepository->ViewBookingbyId($kodebooking);
            if($getBooking->count() < 1){
                $response = array(
                    'message' => 'No. Antrian Tidak Ditemukan.', // Set array status dengan success     
                );
                $metadata = array(
                    'message' => 'failed', // Set array status dengan success    
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return $this->sendError("No. Reservasi Tidak Ditemukan atau Sudah Dibatalkan.", []); 
            }
                $databooking = $getBooking->first(); 
                $batal = $databooking->batal;
                $Datang = $databooking->Datang;
                $NoAntrianAll = $databooking->NoAntrianAll;
                $Antrian = $databooking->Antrian;
                $NoMrfix = $databooking->NoMR;
                $ID_Penjamin = $databooking->ID_Penjamin;
                $NamaDokter= $databooking->NamaDokter;
                $NoMr = str_replace("-", "", $NoMrfix);
                $ApmDate = $databooking->ApmDate;
                $idperusahaan = $databooking->ID_Penjamin;
                $JenisPembayaran = $databooking->JenisPembayaran;
                $NamaPasien = $databooking->NamaPasien;
                $NoKartuBPJS = $databooking->NoKartuBPJS;
                 // validasi checkin, tapi DATA PROVINSI KOSONG
                 $dataMedrecProvinsi = $this->medrecRepository->getMedrecbyNoMR($NoMrfix);
                 $xdataMedrecProvinsi = $dataMedrecProvinsi->first();
                 if ($xdataMedrecProvinsi->kelurahan == null ) {
                     return $this->sendError("Data Medical Record anda belum lengkap, Silahkan ke Counter Pendaftaran untuk melengkapi Data Medical Record Anda.", $databooking); 
                 }
                 if ($xdataMedrecProvinsi->ID_Card_number == "0000000000000000" ) {
                     return $this->sendError("Data Medical Record Identitas anda belum lengkap, Silahkan ke Counter Pendaftaran untuk melengkapi Data Medical Record Anda.", $databooking); 
                 }
                if( $NoMrfix == "" or  $NoMrfix == null or  $NoMrfix == "-"){ 
                    return $this->sendError("No. Medical Record Anda Tidak ada. Anda Harus Melakukan verifikasi Medical Record Terlebih Dahulu.", $databooking); 
                }
                if($JenisPembayaran == "PRIBADI"){
                    $JenisBayar = "1";
                    $kodeRegAwalXX = "RJUM";
                }elseif($JenisPembayaran == "ASURANSI"){
                    $JenisBayar = "2";
                    $kodeRegAwalXX = "RJAS";
                }elseif($JenisPembayaran == "JAMINAN PERUSAHAAN"){
                    $JenisBayar = "5";
                    $kodeRegAwalXX = "RJJP";
                }
                if($idperusahaan == null || $idperusahaan == ""){
                    $Perusahaan = "315";
                }else{
                    $Perusahaan =$databooking->ID_Penjamin;
                }
                $shift = $databooking->JamPraktek;
                $IdDokter = $databooking->DoctorID;
                $NamaGrupPerawatan = $databooking->Poli;
                $IdGrupPerawatan = $databooking->IdPoli;
               
                $ID_JadwalPraktek = $databooking->ID_JadwalPraktek;

                $NoRujukanBPJS = $databooking->NoRujukanBPJS;
                $NoKartuBPJS = $databooking->NoKartuBPJS;
                $NoSuratKontrolBPJS = $databooking->NoSuratKontrolBPJS;
                $NoSuratKontrolBPJS = $databooking->NoSuratKontrolBPJS;
                $NoSEP = $databooking->NoSEP;
                if($Datang > 0){ 
                    return $this->sendError("No. Reservasi Sudah Checkin, No. Reservasi tidak berlaku." , []); 
                }

            // Cek tanggal Reservasi nya untuk kapan, harus di hari yang sama
            $dateNow = Carbon::now()->toDateString();
            if($ApmDate < $dateNow){
                return $this->sendError("Tanggal Reservasi : ". date("d-m-Y", strtotime($ApmDate)) ." Lebih Kecil dari tanggal Hari ini : ". date("d-m-Y", strtotime($dateNow)) . ". Silahkan Cek Kembali Kode Reservasi Anda. Pastikan Tanggal Reservasi Anda adalah tanggal hari ini. ", []);  
            } 
            $dateNow = Carbon::now()->toDateString();
            if($ApmDate > $dateNow){
                return $this->sendError("Tanggal Reservasi : ". date("d-m-Y", strtotime($ApmDate)) ." Lebih Besar dari tanggal Hari ini : ". date("d-m-Y", strtotime($dateNow)). ". Silahkan Cek Kembali Kode Reservasi Anda. Pastikan Tanggal Reservasi Anda adalah tanggal hari ini.", []);  
            } 
           
            //get max visit
            $maxVisit = $this->visitRepository->getMaxnumberVisit();
            $maxVisit->ID++;
            
            $operator = "2852"; 
            $CaraBayar = "5";
 
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
                    return $this->sendError("Pasien Sudah mendaftar di Poli dan Dokter yang sama !" , []); 
                } 
               
            }else{ // JIKA TIDAK ADA REG AKTIF
                $NoregistrationRajal = $this->genNumberRegistrationRajal($datenowcreate,$kodeRegAwalXX,$datenow,$NoMrfix);
                $NoEpisode = $NoregistrationRajal[3];
                $auto_eps = $NoregistrationRajal[4];
                $id_eps = $NoregistrationRajal[5];
                $nofixReg = $NoregistrationRajal[1]; 
                 
            }
                $catatan = "";
                $FisioterapiFlag = '0';
                if($IdGrupPerawatan == "17"){
                    $FisioterapiFlag = '1';
                }else{
                    $FisioterapiFlag = '0';
                }
                $timenow = Carbon::now()->toTimeString();
                
                // INSERT REGISTRATION
                if($JenisPembayaran == "ASURANSI"){
                    $this->visitRepository->addRegistrationRajalAsuransi($maxVisit->ID,$NoEpisode,$nofixReg,$NamaGrupPerawatan,$NoMrfix,
                    $JenisBayar,$IdGrupPerawatan,$IdDokter,$Antrian,$NoAntrianAll,
                    $Company,$shift,$TelemedicineIs,$ApmDate.' '.$timenow,
                    $ApmDate.' '.$timenow,$operator,$CaraBayar,$Perusahaan,$idCaraMasuk,
                    $idAdmin,$Tipe_Registrasi,$ID_JadwalPraktek,$catatan,$FisioterapiFlag);
                }else {
                    $this->visitRepository->addRegistrationRajal($maxVisit->ID,$NoEpisode,$nofixReg,$NamaGrupPerawatan,$NoMrfix,
                    $JenisBayar,$IdGrupPerawatan,$IdDokter,$Antrian,$NoAntrianAll,
                    $Company,$shift,$TelemedicineIs,$ApmDate.' '.$timenow,
                    $ApmDate.' '.$timenow,$operator,$CaraBayar,$Perusahaan,$idCaraMasuk,
                    $idAdmin,$Tipe_Registrasi,$ID_JadwalPraktek,$catatan,$FisioterapiFlag);
                }                                          
                //  UPDATE DATANG RESERVASI
                $this->appointmenRepository->updateDatangAppointment($kodebooking,$nofixReg); 

                $response = array(
                    'NoEpisode' => $NoEpisode, // Set array status dengan success     
                    'NoRegistrasi' => $nofixReg, // Set array status dengan success     
                    'NamaGrupPerawatan' => $NamaGrupPerawatan, // Set array status dengan success     
                    'NOMR' => $NoMrfix, // Set array status dengan success     
                    'Antrian' => $Antrian, // Set array status dengan success     
                    'KodeAsuransi' =>  $Perusahaan, // Set array status dengan success     
                    'GroupJaminan' =>  $JenisBayar, // Set array status dengan success  
                    'ID_Penjamin' => $ID_Penjamin,   
                    'NoAntrianAll' =>  $NoAntrianAll, // Set array status dengan success     
                    'NoRujukanBPJS' =>  $NoRujukanBPJS, // Set array status dengan success     
                    'NoSuratKontrolBPJS' =>  $NoSuratKontrolBPJS, // Set array status dengan success    
                    'NoKartuBPJS'  =>  $NoKartuBPJS, // Set array status dengan success    
                    'NoSEP' =>  $NoSEP, // Set array status dengan success     
                    'NamaDokter' =>  $NamaDokter, // Set array status dengan success     
                    'TglRegistrasi' =>  $ApmDate, // Set array status dengan success  
                    'JamRegistrasi' =>  $timenow, // Set array status dengan success  
                    'jamPraktek'   =>  $shift, // Set array status dengan success  
                    'NamaPasien'   =>  $NamaPasien, // Set array status dengan success  
                );

                DB::connection('sqlsrv3')->commit();
                return $this->sendResponse($response ,"Checkin Pasien Berhasil.");  


        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []); 
        }  
    }  
     
    public function viewAppointmentbyId(Request $request){
      try{
            $data = $this->appointmenRepository->ViewBookingbyId($request->kodebooking);
            $datafirst=$data->first();
            if($data->count() < 1){ 
                return $this->sendError("No. Reservasi Tidak Ditemukan.", []);
            }
            $response = array(
                'DoctorID' => $datafirst->DoctorID, // Set array status dengan success     
                'namapoli' => $datafirst->Poli, // Set array status dengan success     
                'namadokter' => $datafirst->NamaDokter, // Set array status dengan success     
                'JamPraktek' => $datafirst->JamPraktek, // Set array status dengan success     
                'ApmDate' => $datafirst->ApmDate, // Set array status dengan success     
                'NoAntrianAll' => $datafirst->NoAntrianAll, // Set array status dengan success     
                'NoBooking' => $datafirst->NoBooking, // Set array status dengan success     
                'NoRegistrasi' => $datafirst->NoRegistrasi, // Set array status dengan success     
                'NoRujukanBPJS' => $datafirst->NoRujukanBPJS, // Set array status dengan success     
                'NoKartuBPJS' => $datafirst->NoKartuBPJS, // Set array status dengan success     
                'NoSuratKontrolBPJS' => $datafirst->NoSuratKontrolBPJS, // Set array status dengan success     
                'NoSEP'  => $datafirst->NoSEP, // Set array status dengan success     
            ); 
            return $this->sendResponse( $response,"Data ditemukan.");  
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
    public function viewAppointmentbyMedrec(Request $request){
        try{
              $data = $this->appointmenRepository->viewAppointmentbyMedrec($request->NoMR);
               
              if($data->count() < 1){ 
                  return $this->sendError("Anda belum melakukan Reservasi apapun.", []);
              }
              
              return $this->sendResponse( $data,"Data ditemukan.");  
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
      public function viewAppointmentbyUserid_Mobile(Request $request){
        try{
              $data = $this->appointmenRepository->viewAppointmentbyUserid_Mobile($request->Userid_Mobile);
               
              if($data->count() < 1){ 
                  return $this->sendError("Anda belum melakukan Reservasi apapun.", []);
              }
              
              return $this->sendResponse( $data,"Data ditemukan.");  
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
      public function viewAppointmentbyUserid_Mobile_All(Request $request){
        try{
              $data = $this->appointmenRepository->viewAppointmentbyUserid_Mobile_All($request->Userid_Mobile);
               
              if($data->count() < 1){ 
                  return $this->sendError("Anda belum melakukan Reservasi apapun.", []);
              }
              
              return $this->sendResponse( $data,"Data ditemukan.");  
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
