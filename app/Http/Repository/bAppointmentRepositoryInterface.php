<?php
namespace App\Http\Repository;
interface bAppointmentRepositoryInterface
{ 
    public function AmbilAntrian($request,$JenisBoking,$idbooking,$nouruttrx,$TglLahir,$JnsKelamin,
    $StatusNikahPasien,$IdGrupPerawatan,$NamaGrupPerawatan,$IdDokter,
    $NamaDokter,$NamaSesion,$idno_urutantrian,
    $fixNoAntrian,$NamaPasien,$tglbookingfix,$nobokingreal,
    $xres,$MrExist,$Company,$kodejenispayment,$NoTlp,$NoHp,$Alamat,$datenowcreate,
    $noteall,$txEmail,$NoMrfix,$ID_Penjamin,$ID_JadwalPraktek,$Userid_Mobile,$noRujukan,
    $estimasi_waktu_pelayanan,$lama_menit_dilayani); 
    public function SisaStatusAntrian($request); 
    public function StatusAntrian($request); 
    public function BatalAntrian($request); 
    public function CheckIn($request); 
    public function UpdateTaskID($request); 
    public function ViewBookingbyId($request); 
}