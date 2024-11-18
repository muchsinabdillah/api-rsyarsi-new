<?php

namespace App\Http\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class bAppointmentRepositoryImpl implements bAppointmentRepositoryInterface
{
    public function AmbilAntrian($request,$JenisBoking,$idbooking,$nouruttrx,$TglLahir,$JnsKelamin,
                                $StatusNikahPasien,$IdGrupPerawatan,$NamaGrupPerawatan,$IdDokter,
                                $NamaDokter,$NamaSesion,$idno_urutantrian,
                                $fixNoAntrian,$NamaPasien,$tglbookingfix,$nobokingreal,
                                $xres,$MrExist,$Company,$kodejenispayment,$NoTlp,$NoHp,$Alamat,$datenowcreate,
                                $noteall,$txEmail,$NoMrfix,$ID_Penjamin,$ID_JadwalPraktek,
                                $Userid_Mobile,$noRujukan,$estimasi_waktu_pelayanan,$lama_menit_dilayani)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")->insert([
            'CodeReservasi' => $idbooking,
            'CategoriReservasi' => $JenisBoking,
            'NoUrut' =>  $nouruttrx,
            'NoMR' => $NoMrfix,
            'TglLahir' =>  $TglLahir,
            'JenisKelamin' => $JnsKelamin,
            'StatusMenikah' =>  $StatusNikahPasien,
            'IdPoli' => $IdGrupPerawatan,
            'Poli' =>  $NamaGrupPerawatan,
            'DoctorID' => $IdDokter,
            'NamaDokter' => $NamaDokter, 
            'Status' => '1', 
            'JamPraktek' => $NamaSesion, 
            'Antrian' => $idno_urutantrian, 
            'NoAntrianAll' => $fixNoAntrian, 
            'NamaPasien' => $NamaPasien, 
            'ApmDate' => $tglbookingfix, 
            'NoBooking' => $nobokingreal, 
            'NoReservasi' => $xres, 
            'MrExist' => $MrExist, 
            'Company' => $Company, 
            'JenisPembayaran' => $kodejenispayment, 
            'Telephone' => $NoTlp, 
            'HP' => $NoHp, 
            'Alamat' => $Alamat, 
            'DateCreate' => $datenowcreate, 
            'Description' => $noteall, 
            'Email' => $txEmail,
            'ID_JadwalPraktek' => $ID_JadwalPraktek,
            'ID_Penjamin' => $ID_Penjamin,
            'Userid_Mobile' => $Userid_Mobile,
            'NoRujukanBPJS' => $noRujukan,
            'estimasi_waktu_dilayani' => $estimasi_waktu_pelayanan,
            'estimasi_waktu_pelayanan' => $lama_menit_dilayani
        ]);
    } 
    public function SisaStatusAntrian($request)
    {
         
    } 
    public function StatusAntrian($request)
    {
         
    } 
    public function BatalAntrian($nobooking)
    {
        return DB::connection('sqlsrv3')->table('Apointment')
            ->where('NoBooking', $nobooking)
            ->update([
            'batal' => '1',
            'jam_batal' => Carbon::now()
        ]);
      
    } 
    public function CheckIn($request)
    {
         
    } 
    public function UpdateTaskID($request)
    {
         
    } 
    public function ViewBookingbyId($nobooking)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")
        ->select( 'DoctorID','JamPraktek','NamaDokter','IdPoli','Poli','JamPraktek','JenisPembayaran','ID_Penjamin',
                    'NoAntrianAll','Antrian','NoBooking','NamaPasien','batal', 'Datang','NoMR','Company','ID_JadwalPraktek',
                    DB::raw("replace(CONVERT(VARCHAR(11), ApmDate, 111), '/','-') ApmDate"),"NoRegistrasi","NoRujukanBPJS","NoKartuBPJS","NoSuratKontrolBPJS","NamaPasien" ,"NoSEP",DB::raw("DATEDIFF(year,TglLahir,GETDATE()) umurtahun"))
        ->where('NoBooking', $nobooking)
        ->where('batal', '0')
        ->get();
    } 
    public function viewAppointmentbyMedrec($NoMR)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")
        ->select(  'DoctorID','JamPraktek','NamaDokter','IdPoli','Poli','JamPraktek','JenisPembayaran','ID_Penjamin',
        'NoAntrianAll','Antrian','NoBooking','NamaPasien','batal', 'Datang','NoMR','Company','ID_JadwalPraktek',
                    DB::raw("replace(CONVERT(VARCHAR(11), ApmDate, 111), '/','-') ApmDate") ,"NoRegistrasi","NoRujukanBPJS","NoKartuBPJS","NoSuratKontrolBPJS","NamaPasien","NoSEP" )
        ->where('NoMR', $NoMR)
        ->where('batal', '0')
        ->where('Datang', '0')
        ->orderBy('ApmDate','desc')
        ->get();
    } 
    public function viewAppointmentbyUserid_Mobile($Userid_Mobile)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")
        ->select(  'DoctorID','JamPraktek','NamaDokter','IdPoli','Poli','JamPraktek','JenisPembayaran','ID_Penjamin',
        'NoAntrianAll','Antrian','NoBooking','NamaPasien','batal', 'Datang','NoMR','Company','ID_JadwalPraktek',
                    DB::raw("replace(CONVERT(VARCHAR(11), ApmDate, 111), '/','-') ApmDate") ,"NoRegistrasi","NoRujukanBPJS","NoKartuBPJS","NoSuratKontrolBPJS","NamaPasien" )
        ->where('Userid_Mobile', $Userid_Mobile)
        ->where('batal', '0')
        ->where('Datang', '0')
        ->orderBy('ApmDate','desc')
        ->get();
    }
    public function viewAppointmentbyUserid_Mobile_All($Userid_Mobile)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")
        ->select(  'DoctorID','JamPraktek','NamaDokter','IdPoli','Poli','JamPraktek','JenisPembayaran','ID_Penjamin',
        'NoAntrianAll','Antrian','NoBooking','NamaPasien','batal', 'Datang','NoMR','Company','ID_JadwalPraktek',
                    DB::raw("replace(CONVERT(VARCHAR(11), ApmDate, 111), '/','-') ApmDate") ,"NoRegistrasi","NoRujukanBPJS","NoKartuBPJS","NoSuratKontrolBPJS","NamaPasien" )
        ->where('Userid_Mobile', $Userid_Mobile) 
        ->orderBy('ApmDate','desc')
        ->get();
    }
    public function getMaxAppointmentNumber(){
        return  DB::connection('sqlsrv3')->table("Apointment")->find(DB::connection('sqlsrv3')->table("Apointment")->max('ID'));
    }

    public function getBookingCurrentTIme($tglbookingfix,$IdGrupPerawatan,$IdDokter,$NoMrfix)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")
        ->select( 'NoBooking',  'NoAntrianAll')
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), ApmDate, 111), '/','-')"),$tglbookingfix)   
        ->where('idPoli', $IdGrupPerawatan)
        ->where('NoMR', $NoMrfix)
        ->where('DoctorID', $IdDokter)
        ->where('Batal', '0')
        ->where('Datang', '0')
        ->get();
    }
    public function getBookingCurrentTImeWeb($tglbookingfix,$IdGrupPerawatan,$IdDokter,$NoMrfix,$NoHp)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")
        ->select( 'NoBooking',  'NoAntrianAll')
        ->where('NoMR', $NoMrfix)
        ->where('HP', $NoHp)
        ->whereIn('Company', ['WEBSITE YARSI', 'RSYARSI MOBILE APP'])
        ->where('Batal', '0')
        ->where('Datang', '0')
        ->get();
    }
    public function generateNoBookingTrs($tglbookingfix)
    {
        return  DB::connection('sqlsrv3')->table("Apointment")
        ->select( 'NoBooking','NoUrut',DB::raw('right(NoBooking,3) as urut ') )
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), ApmDate, 111), '/','-')"),$tglbookingfix)   
        ->orderBy('nourut', 'desc')->first();
    }
    public function updateDatangAppointment($NoBooking,$NoRegistrasi){
        $updatesatuan =  DB::connection('sqlsrv3')->table('Apointment')
        ->where('NoBooking', $NoBooking) 
            ->update([
                'datang' => '1',
                'NoRegistrasi' =>  $NoRegistrasi
            ]);
        return $updatesatuan;
    }
    public function updateNoMrAppointment($NoBooking,$NoMR){
        $updatesatuan =  DB::connection('sqlsrv3')->table('Apointment')
        ->where('NoBooking', $NoBooking) 
            ->update([ 
                'NoMR' =>  $NoMR
            ]);
        return $updatesatuan;
    }
   
}
