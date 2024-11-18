<?php

namespace App\Http\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class bVisitRepositoryImpl implements bVisitRepositoryInterface
{
    public function getMaxnumberVisit()
    {
        return  DB::connection('sqlsrv3')->table("Visit")
        ->select('ID')
        ->orderBy('ID', 'desc')->first();
    }
    public function getRegistrationActivebyMedrec($medrec)
    {
        return  DB::connection('sqlsrv3')->table("Visit")
        ->select('Status ID','NoEpisode')
        ->where('NoMR',$medrec)
        ->where('batal','0')
        ->where('Status ID','<>','4')
        ->orderBy('ID', 'desc')->first();
    }
    public function getRegistrationRajalbyMedreActive($medrec)
    {
        //a
        return  DB::connection('sqlsrv6')->table("dataRWJ")
        ->select('NoAntrianAll','NamaJaminan','PatientName',DB::raw("CASE WHEN Sex='L' then 'M' ELSE 'F' END AS Gander") ,
        DB::raw("replace(CONVERT(VARCHAR(11), DateOfBirth, 111), '/','-') as Date_of_birth") , 
        'Address',   'IdUnit', DB::raw("[Visit Date] AS Visit_Date"), 'NamaUnit',   'IdDokter', 'NamaDokter','NoMR','NoEpisode','NoRegistrasi',
        DB::raw("case when TipePasien='1' THEN 'PRIBADI' WHEN TipePasien='2' THEN 'ASURANSI' WHEN TipePasien='5' THEN 'PERUSAHAAN' END 
        AS  PatientType"),'StatusID')
        ->where('NoMR',$medrec)
         ->where('StatusID','<',4)
        ->orderBy('Visit Date', 'desc')
        ->get();
    }
    public function getRegistrationMCUbyDate($request)
    {
        //a
        return  DB::connection('sqlsrv6')->table("dataRWJ")
        ->select('NoEpisode','NoRegistrasi','NoMR','PatientName',DB::raw("replace(CONVERT(VARCHAR(11),[Visit Date], 111), '/','-') AS VisitDate") ,
        'DateOfBirth',  'Sex','NamaDokter')
        ->where('IdUnit','53')
        ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), [Visit Date], 111), '/','-')"),
         [$request->tglPeriodeBerobatAwal,$request->tglPeriodeBerobatAkhir])  
        ->orderBy('Visit Date', 'desc')
        ->get();
    }
    public function getRegistrationRajalbyMedreHistory($request)
    {
        //a
        return  DB::connection('sqlsrv6')->table("dataRWJ")
        ->select( 'NoAntrianAll', 'NamaJaminan','StatusID','PatientName',DB::raw("CASE WHEN Sex='L' then 'M' ELSE 'F' END AS Gander") ,
        DB::raw("replace(CONVERT(VARCHAR(11), DateOfBirth, 111), '/','-') as Date_of_birth") , 
        'Address',   'IdUnit', DB::raw("[Visit Date] AS Visit_Date"), 'NamaUnit',   'IdDokter', 'NamaDokter','NoMR','NoEpisode','NoRegistrasi',
        DB::raw("case when TipePasien='1' THEN 'PRIBADI' WHEN TipePasien='2' THEN 'ASURANSI' WHEN TipePasien='5' THEN 'PERUSAHAAN' END 
        AS  PatientType"),'StatusID')
        ->where('NoMR',$request->medrec)
         ->where('StatusID','4')
         ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), [Visit Date], 111), '/','-')"),
         [$request->tglPeriodeBerobatAwal,$request->tglPeriodeBerobatAkhir])  
        ->orderBy('Visit Date', 'desc')
        ->get();
    }
    public function getRegistrationRajalbyDoctorActive($NamaDokter)
    {
        //a
        return  DB::connection('sqlsrv6')->table("dataRWJ")
        ->select('NoAntrianAll',  'NamaJaminan','PatientName',DB::raw("CASE WHEN Sex='L' then 'M' ELSE 'F' END AS Gander") ,
        DB::raw("replace(CONVERT(VARCHAR(11), DateOfBirth, 111), '/','-') as Date_of_birth") , 
        'Address',   'IdUnit', DB::raw("[Visit Date] AS Visit_Date"), 'NamaUnit',   'IdDokter', 'NamaDokter','NoMR','NoEpisode','NoRegistrasi',
        DB::raw("case when TipePasien='1' THEN 'PRIBADI' WHEN TipePasien='2' THEN 'ASURANSI' WHEN TipePasien='5' THEN 'PERUSAHAAN' END 
        AS  PatientType"),'StatusID')
        ->where('NamaDokter',$NamaDokter)
         ->where('StatusID','<',4)
        ->orderBy('Visit Date', 'desc')
        ->get();
    }
    public function getRegistrationRajalbyDoctorHistory($request)
    {
        //a
        return  DB::connection('sqlsrv6')->table("dataRWJ")
        ->select('NoAntrianAll', 'NamaJaminan','StatusID','PatientName',DB::raw("CASE WHEN Sex='L' then 'M' ELSE 'F' END AS Gander") ,
        DB::raw("replace(CONVERT(VARCHAR(11), DateOfBirth, 111), '/','-') as Date_of_birth") , 
        'Address',   'IdUnit',  DB::raw("[Visit Date] AS Visit_Date"), 'NamaUnit',   'IdDokter', 'NamaDokter','NoMR','NoEpisode','NoRegistrasi',
        DB::raw("case when TipePasien='1' THEN 'PRIBADI' WHEN TipePasien='2' THEN 'ASURANSI' WHEN TipePasien='5' THEN 'PERUSAHAAN' END 
        AS  PatientType"),'StatusID')
        ->where('NamaDokter',$request->NamaDokter)
        ->where('StatusID','4')
        ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), [Visit Date], 111), '/','-')"),
        [$request->tglPeriodeBerobatAwal,$request->tglPeriodeBerobatAkhir])  
        ->orderBy('Visit Date', 'desc')
        ->get();
    }
    public function getRegistrationRajalbyNoreg($NoRegistrasi)
    {
        //a
        return  DB::connection('sqlsrv6')->table("viewRegistrasiPasienbyNoreg")
        ->where('NoRegistrasi', $NoRegistrasi) 
        ->orderBy('Visit_Date', 'desc')
        ->get();
    }
    public function getAppointmentNumber($NoBooking)
    {
        //a
        return  DB::connection('sqlsrv3')->table("View_RegistrasiByAppointmentNumber") 
        ->where('NoBooking', $NoBooking)  
        ->first();
    }
    public function getRegistrationLastByDate($tglregistrasi,$codeRegAwal){
        return  DB::connection('sqlsrv3')->table("Visit")
        ->select('NoRegistrasi','NoEpisode',DB::raw("right( REPLACE(NoRegistrasi,'-','0') ,4) as urutregx"),
                    DB::raw("right( REPLACE(NoEpisode,'-','0')  ,4) as noepisodex"))
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), TglKunjungan, 111), '/','-')"),$tglregistrasi)  
        ->where(DB::raw("LEFT(NoRegistrasi,4)"),$codeRegAwal)   
        ->orderBy('id', 'desc')->first();
        
    }
    public function getActiveRegistrationToday($NoMR,$IdGrupPerawatan,$IdDokter,$shift,$ApmDate)
    {
        return  DB::connection('sqlsrv3')->table("Visit")
        ->select('Status ID','NoEpisode','NoRegistrasi')
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), TglKunjungan, 111), '/','-')"),$ApmDate)  
        ->where('NoMR',$NoMR)   
        ->where('Batal','0')
        ->where('Status ID','<>','4')   
        ->where('unit',$IdGrupPerawatan)   
        ->where('Doctor_1',$IdDokter)   
        ->where('JamPraktek',$shift)   
        ->orderBy('id', 'desc')->first();
    }
    public function  addRegistrationRajal($ID,$NoEpisode,$NoRegistrasi,$LokasiPasien,$NoMR,
                                            $PatientType,$Unit,$Doctor_1,$Antrian,$NoAntrianAll,
                                            $Company,$JamPraktek,$TelemedicineIs,$TglKunjungan,
                                            $visitdate,$Operator,$CaraBayar,$Perusahaan,$idCaraMasuk,
                                            $idAdmin,$Tipe_Registrasi,$ID_JadwalPraktek,$Catatan,$FisioterapiFlag)
    {
        return  DB::connection('sqlsrv3')->table("Visit")->insert([
            'ID' => $ID,
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' =>  $NoRegistrasi,
            'LokasiPasien' => $LokasiPasien,
            'NoMR' =>  $NoMR,
            'PatientType' => $PatientType,
            'Unit' =>  $Unit,
            'Doctor_1' => $Doctor_1,
            'Antrian' =>  $Antrian,
            'NoAntrianAll' => $NoAntrianAll,
            'Company' => $Company, 
            'JamPraktek' => $JamPraktek, 
            'TelemedicineIs' => $TelemedicineIs, 
            'TglKunjungan' => $TglKunjungan, 
            'Visit Date' => $visitdate, 
            'Operator' => $Operator, 
            'CaraBayar' => $CaraBayar, 
            'Perusahaan' => $Perusahaan, 
            'idCaraMasuk' => $idCaraMasuk, 
            'idAdmin' => $idAdmin, 
            'Tipe_Registrasi' => $Tipe_Registrasi, 
            'ID_JadwalPraktek' => $ID_JadwalPraktek,
            'Catatan' => $Catatan,
            'FisioterapiFlag' => $FisioterapiFlag
        ]);
    }
    public function  addRegistrationRajalAsuransi($ID,$NoEpisode,$NoRegistrasi,$LokasiPasien,$NoMR,
                                            $PatientType,$Unit,$Doctor_1,$Antrian,$NoAntrianAll,
                                            $Company,$JamPraktek,$TelemedicineIs,$TglKunjungan,
                                            $visitdate,$Operator,$CaraBayar,$Perusahaan,$idCaraMasuk,
                                            $idAdmin,$Tipe_Registrasi,$ID_JadwalPraktek,$Catatan,$FisioterapiFlag)
    {
        return  DB::connection('sqlsrv3')->table("Visit")->insert([
            'ID' => $ID,
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' =>  $NoRegistrasi,
            'LokasiPasien' => $LokasiPasien,
            'NoMR' =>  $NoMR,
            'PatientType' => $PatientType,
            'Unit' =>  $Unit,
            'Doctor_1' => $Doctor_1,
            'Antrian' =>  $Antrian,
            'NoAntrianAll' => $NoAntrianAll,
            'Company' => $Company, 
            'JamPraktek' => $JamPraktek, 
            'TelemedicineIs' => $TelemedicineIs, 
            'TglKunjungan' => $TglKunjungan, 
            'Visit Date' => $visitdate, 
            'Operator' => $Operator, 
            'CaraBayar' => $CaraBayar, 
            'Asuransi' => $Perusahaan, 
            'idCaraMasuk' => $idCaraMasuk, 
            'idAdmin' => $idAdmin, 
            'Tipe_Registrasi' => $Tipe_Registrasi, 
            'ID_JadwalPraktek' => $ID_JadwalPraktek,
            'Catatan' => $Catatan,
            'FisioterapiFlag' => $FisioterapiFlag
        ]);
    }
    public function addTaskOneBPJS($KODE_TRANSAKSI,$WAKTU,$TASK_ID,$DATE_CREATE)
    {
        return  DB::connection('sqlsrv3')->table("BPJS_TASKID_LOG")->insert([
            'KODE_TRANSAKSI' => $KODE_TRANSAKSI,
            'WAKTU' => $WAKTU,
            'TASK_ID' =>  $TASK_ID,
            'DATE_CREATE' => $DATE_CREATE
        ]);
    }
    public function viewByNoBooking($NoBooking)
    {
        //a
        return  DB::connection('sqlsrv3')->table("View_Visit_by_Booking") 
        ->where('NoBooking', $NoBooking)  
        ->get();
    }
    public function updateNoSepbyNoRegistrasi($request)
    {
        $updatesatuan =  DB::connection('sqlsrv3')->table('Visit')
        ->where('NoRegistrasi', $request->NoRegistrasi)
            ->update([
                'NoSEP' => $request->NoSep,
                'NoPesertaBPJS' => $request->NoKartu
            ]);
        return $updatesatuan;
    }
    public function updateNoSepbyNoRegistrasi2($request)
    {
        $updatesatuan =  DB::connection('sqlsrv6')->table('dataRWJ')
        ->where('NoRegistrasi', $request->NoRegistrasi)
            ->update([
                'NoSep' => $request->NoSep,
                'NoPesertaBPJS' => $request->NoKartu
            ]);
        return $updatesatuan;
    }
    public function  addSEP($request)
    {
        return  DB::connection('sqlsrv3')->table("BPJS_T_SEP")->insert([
            
            
            'NO_SEP' => $request->NO_SEP, 
            'NO_REGISTRASI' => $request->NO_REGISTRASI, 
            'NO_KARTU' => $request->NO_KARTU, 
            'TGL_SEP' => $request->TGL_SEP, 
            'NO_MR' => $request->NO_MR, 
            'NAMA_PESERTA' => $request->NAMA_PESERTA, 
            'JENIS_KELAMIN' => $request->JENIS_KELAMIN, 
            'JENIS_PESERTA' => $request->JENIS_PESERTA, 
            'COB' => $request->COB, 
            'JENIS_RAWAT' => $request->JENIS_RAWAT, 
            'KODE_POLI' => $request->KODE_POLI, 
            'NAMA_POLI' => $request->NAMA_POLI, 
            'KODE_DOKTER' => $request->KODE_DOKTER, 
            'NAMA_DOKTER' => $request->NAMA_DOKTER, 
            'KODE_DIAGNOSA' => $request->KODE_DIAGNOSA, 
            'NAMA_DIAGNOSA' => $request->NAMA_DIAGNOSA, 
            'NO_TELEPON' => $request->NO_TELEPON, 
            'PENJAMIN' => $request->PENJAMIN, 
            'KELAS_RAWAT' => $request->KELAS_RAWAT, 
            'CATATAN' => $request->CATATAN, 
            'TGL_LAHIR' => $request->TGL_LAHIR, 
            'KODE_PERUJUK' => $request->KODE_PERUJUK, 
            'NAMA_PERUJUK' => $request->NAMA_PERUJUK, 
            'NO_RUJUKAN' => $request->NO_RUJUKAN, 
            'NO_SPRI' => $request->NO_SPRI, 
            'NO_NIK' => $request->NO_NIK, 
            'KODE_JENIS_PESERTA' => $request->KODE_JENIS_PESERTA, 
            'IS_EKSEKUTIF' => $request->IS_EKSEKUTIF, 
            'IS_KATARAK' => $request->IS_KATARAK, 
            'IS_COB' => $request->IS_COB, 
            'COB_NO_ASURANSI' => $request->COB_NO_ASURANSI, 
            'KODE_JENIS_RAWAT' => $request->KODE_JENIS_RAWAT, 
            'NAIK_KELAS' => $request->NAIK_KELAS, 
            'NAIK_KELAS_ID' => $request->NAIK_KELAS_ID, 
            'PENANGGUNG_JAWAB' => $request->PENANGGUNG_JAWAB, 
            'KODE_KELAS_RAWAT' => $request->KODE_KELAS_RAWAT, 
            'KODE_PPK_PERUJUK' => $request->KODE_PPK_PERUJUK, 
            'NAMA_PPK_PERUJUK' => $request->NAMA_PPK_PERUJUK, 
            'KETERANGAN_PRB' => $request->KETERANGAN_PRB, 
            'TUJUAN_KUNJUNGAN' => $request->TUJUAN_KUNJUNGAN, 
            'FLAG_PROCEDURE' => $request->FLAG_PROCEDURE, 
            'PENUNJANG' => $request->PENUNJANG, 
            'ASESMENT_PELAYANAN' => $request->ASESMENT_PELAYANAN, 
            'IS_LAKA_LANTAS' => $request->IS_LAKA_LANTAS, 
            'TGL_LAKA_LANTAS' => $request->TGL_LAKA_LANTAS, 
            'KET_LAKA_LANTAS' => $request->KET_LAKA_LANTAS, 
            'IS_SUPLESI' => $request->IS_SUPLESI, 
            'NO_SUPLESI' => $request->NO_SUPLESI, 
            'PROV_KODE' => $request->PROV_KODE, 
            'PROV_NAMA' => $request->PROV_NAMA, 
            'KABUPATEN_KODE' => $request->KABUPATEN_KODE, 
            'KABUPATEN_NAMA' => $request->KABUPATEN_NAMA, 
            'KECAMATAN_KODE' => $request->KECAMATAN_KODE, 
            'KECAMATAN_NAMA' => $request->KECAMATAN_NAMA, 
            'KODE_ASAL_FASKES' => $request->KODE_ASAL_FASKES, 
            'NAMA_ASAL_FASKES' => $request->NAMA_ASAL_FASKES, 
            'TGL_RUJUKAN' => $request->TGL_RUJUKAN, 
            'TGL_CREATE' => $request->TGL_CREATE, 
            'USER_CREATE' => $request->USER_CREATE ,
            'Task1' => $request->Task1 ,
        ]);
    }
    public function viewsep($NoRegistrasi)
    { 
        return  DB::connection('sqlsrv3')->table("View_SEP") 
        ->where('NO_REGISTRASI', $NoRegistrasi)  
        ->get();
    }
    public function viewsepBefore($NoRegistrasi)
    { 
        return  DB::connection('sqlsrv3')->table("View_SEP_Before") 
        ->where('NO_REGISTRASI', $NoRegistrasi)  
        ->get();
    }
    public function addTaskBPJS($request)
    {
        return  DB::connection('sqlsrv3')->table("BPJS_TASKID_LOG")->insert([
            'KODE_TRANSAKSI' => $request->KODE_TRANSAKSI,
            'WAKTU' => $request->WAKTU,
            'TASK_ID' =>  $request->TASK_ID,
            'DATE_CREATE' => $request->DATE_CREATE
        ]);
    }
    public function  addDatRWJDashboard($NoEpisode,$NoRegistrasi,$NoMR,$PatientName,
    $VisitDate,$KelurahanName,$KecamatanName,$kabupatenNama,$ProvinceName,$BL,$JenisBayar,
    $perusahaanid,$namaPerusahaan,$Gander,$DateOfBirth,$TipeIdCard,$NoIdCard,$Address,$MaritalStatus,$Religion,
    $Education,$HomePhone,$MobilePhone,$Pekerjaan,$KodePos,$IdUnit,$NamaUnit,$IdDokter,$NamaDokter,
    $NoPesertaBPJS,$NoSep,$JamPraktek,$OperatorId,
    $JenisDaftar,$Telemedicine,$Appointment_ID,$Tipe_Registrasi,$Id_Jadwal_Praktek,
    $idCaraMasuk,$referralid,$idAdmin,$NoAntrianAll,$Company,$NamaCaraMasuk,$NamaCaraMasukRef,$NamaKarcis,
    $NilaiKarcis,$hosnamedata,$datatimestamp,$Bahasa,$Etnis)
    {
        return  DB::connection('sqlsrv6')->table("dataRWJ")->insert([
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' => $NoRegistrasi,
            'NoMR' => $NoMR,
            'PatientName' => $PatientName,
            'VisitDate' => $VisitDate,
            'Kelurahan' => $KelurahanName,
            'Kecamatan' => $KecamatanName,
            'CityName' => $kabupatenNama,
            'ProvinsiNama' => $ProvinceName,
            'BL' => $BL,
            'FP' => '1',
            'TipePasien' => $JenisBayar,
            'KodeJaminan' => $perusahaanid,
            'NamaJaminan' => $namaPerusahaan,
            'Sex' => $Gander,
            'DateOfBirth' => $DateOfBirth,
            'TipeIdCard' => $TipeIdCard,
            'NoIdCard' => $NoIdCard,
            'Address' => $Address,
            'MaritalStatus' => $MaritalStatus,
            'Religion' => $Religion,
            'Education' => $Education,
            'HomePhone' => $HomePhone,
            'MobilePhone' => $MobilePhone,
            'Pekerjaan' => $Pekerjaan,
            'KodePos' => $KodePos,
            'IdUnit' => $IdUnit,
            'NamaUnit' => $NamaUnit,
            'IdDokter' => $IdDokter,
            'NamaDokter' => $NamaDokter,
            'NoPesertaBPJS' => $NoPesertaBPJS,
            'NoSep' => $NoSep,
            'JamPraktek' => $JamPraktek,
            'StatusID' => '0',
            'OperatorId' => $OperatorId,
            'LockBill' => '0',
            'JenisDaftar' => $JenisDaftar,
            'Telemedicine' => $Telemedicine,
            'Appointment_ID' => $Appointment_ID,
            'Tipe_Registrasi' => $Tipe_Registrasi,
            'Id_Jadwal_Praktek' => $Id_Jadwal_Praktek,
            'idCaraMasuk' => $idCaraMasuk,
            'idCaraMasuk2' => $referralid,
            'idAdmin' => $idAdmin,
            'NoAntrianAll' => $NoAntrianAll,
            'Company' => $Company,
            'NamaCaraMasuk' => $NamaCaraMasuk,
            'NamaCaraMasukRef' => $NamaCaraMasukRef,
            'NamaKarcis' => $NamaKarcis,
            'NilaiKarcis' => $NilaiKarcis,
            'Bahasa' => $Bahasa,
            'Etnis' => $Etnis,
            'datatimestamp' => $datatimestamp,
            'hosnamedata' => $hosnamedata
        ]);
    }
    public function getRegistrationRanapbyNoreg($NoRegistrasi)
    {
        //a
        return  DB::connection('sqlsrv6')->table("viewRegistrasiPasienRanap")
        // ->select( DB::raw('Null as NoAntrianAll'), 'NamaJaminan','PatientName',DB::raw("CASE WHEN Sex='L' then 'M' ELSE 'F' END AS Gander") ,DB::raw('DATEDIFF(year, DateOfBirth, GETDATE()) AS Usia'),
        // DB::raw("replace(CONVERT(VARCHAR(11), DateOfBirth, 111), '/','-') as Date_of_birth") , 
        // 'Address',  DB::raw('Null as IdUnit'),  DB::raw("[StartDate] AS Visit_Date"), DB::raw("Null AS NamaUnit"), DB::raw("0 AS IdDokter"), DB::raw("null AS NamaDokter"),'NoMR','NoEpisode','NoRegistrasi',
        // DB::raw("case when TipePasien='1' THEN 'PRIBADI' WHEN TipePasien='2' THEN 'ASURANSI' WHEN TipePasien='5' THEN 'PERUSAHAAN' END 
        // AS  PatientType"),'StatusID','MobilePhone','Kelurahan','Kecamatan','kabupatenNama','ProvinsiNama','TipePasien','KodeJaminan','KelasID_Akhir')
        ->where('NoRegistrasi', $NoRegistrasi) 
        ->orderBy('Visit_Date', 'desc')
        ->get();
    }

    public function voidRegistrasivisit($request)
    {
        $updatesatuan =  DB::connection('sqlsrv3')->table('Visit')
        ->where('NoRegistrasi', $request->NoRegistrasi)
            ->update([
                'Batal' => '1',
                'tglBatal' =>  Carbon::now() ,
                'PetugasBatal' => $request->UserVoid,
                'Keterangan' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }

    public function voidRegistrasidashboard($request)
    {
        $updatesatuan =  DB::connection('sqlsrv6')->table('DataRWJ')
        ->where('NoRegistrasi', $request->NoRegistrasi)
            ->update([
                'Batal' => '1',
                'tglBatal' =>  Carbon::now() ,
                'PetugasBatal' => $request->UserVoid,
            ]);
        return $updatesatuan;
    }
}

