<?php

namespace App\Http\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class bAssesmentRajalRepositoryImpl implements bAssesmentRepositoryInterface
{
    public function CreateAssesmentRajal( $NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$NamaPasien,
                                            $KeluhanPasien, $S_Anamnesa,$S_RPD,
                                            $O_PemeriksaanFisik, $A_Diagnosa, $P_RencanaTatalaksana,$P_InstruksiNonMedis ,
                                            $NamaUser,$GroupUser,$SBAR,$Beratbadan,$TinggiBadan,$Suhu,$FrekuensiNafas
                                            ,$TD_Sistol,$TD_Distol,$FrekuensiNadi,$AlatBantu,$Prothesa,$Cacat
                                            )
    {
        return  DB::connection('sqlsrv5')->table("EMR_RWJ")->insert([
            'NoMR' => $NoMR,
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' =>  $NoRegistrasi,
            'NoMR' => $NoMR,
            'Tgl' =>  $Tgl,
            'NamaPasien' => $NamaPasien, 
            'KeluhanPasien' =>  $KeluhanPasien, 
            'S_Anamnesa' =>  $S_Anamnesa, 
            'S_RPD' =>  $S_RPD, 
            'O_PemeriksaanFisik' =>  $O_PemeriksaanFisik, 
            'A_Diagnosa' =>  $A_Diagnosa, 
            'P_RencanaTatalaksana' =>  $P_RencanaTatalaksana, 
            'P_InstruksiNonMedis' =>  $P_InstruksiNonMedis, 
            'NamaUser' =>  $NamaUser, 
            'GroupUser' =>  $GroupUser, 
            'SBAR' =>  $SBAR, 
            'Beratbadan' =>  $Beratbadan, 
            'TinggiBadan' =>  $TinggiBadan, 
            'Suhu' =>  $Suhu, 
            'FrekuensiNafas' =>  $FrekuensiNafas, 
            'TD_Sistol' => $TD_Sistol,
            'TD_Distol' => $TD_Distol,
            'FrekuensiNadi' => $FrekuensiNadi,
            'AlatBantu' => $AlatBantu,
            'Prothesa' => $Prothesa,
            'Cacat' => $Cacat
        ]);
    }
    public function getAssesment_Rajal_Dokter($NoRegistrasi){ 
        return  DB::connection('sqlsrv5')->table("EMR_RWJ")
        ->select('ID','NoMR'  ,
        'NoEpisode'  ,
        'NoRegistrasi' ,
        'NoMR'  ,
        'Tgl'  ,
        'NamaPasien'  , 
        'KeluhanPasien' , 
        'S_Anamnesa' ,
        'S_RPD' , 
        'O_PemeriksaanFisik'  , 
        'A_Diagnosa' , 
        'P_RencanaTatalaksana' , 
        'P_InstruksiNonMedis' , 
        'NamaUser' , 
        'GroupUser' , 
        'SBAR' , 
        'Beratbadan' , 
        'TinggiBadan'   , 
        'Suhu' , 
        'FrekuensiNafas' , 
        'TD_Sistol'  ,
        'TD_Distol'  ,
        'FrekuensiNadi' ,
        'AlatBantu' ,
        'Prothesa'  ,
        'Cacat' ) 
        ->where('NoRegistrasi', $NoRegistrasi)
        ->where('GroupUser', 'Dokter') 
        ->where('YgMelapor', null) 
        ->where('batal', '0')
        ->orderBy('ID','desc')
        ->get();
    }
    public function UpdateAssesmentRajal( $ID,$NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$NamaPasien,
                                            $KeluhanPasien, $S_Anamnesa,$S_RPD,
                                            $O_PemeriksaanFisik, $A_Diagnosa, $P_RencanaTatalaksana,$P_InstruksiNonMedis ,
                                            $NamaUser,$GroupUser,$SBAR,$Beratbadan,$TinggiBadan,$Suhu,$FrekuensiNafas
                                            ,$TD_Sistol,$TD_Distol,$FrekuensiNadi,$AlatBantu,$Prothesa,$Cacat
                                            )
    {
        return  DB::connection('sqlsrv5')->table("EMR_RWJ")
        ->where('ID', $ID)
        ->update([
            'NoMR' => $NoMR,
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' =>  $NoRegistrasi,
            'NoMR' => $NoMR,
            'Tgl' =>  $Tgl,
            'NamaPasien' => $NamaPasien, 
            'KeluhanPasien' =>  $KeluhanPasien, 
            'S_Anamnesa' =>  $S_Anamnesa, 
            'S_RPD' =>  $S_RPD, 
            'O_PemeriksaanFisik' =>  $O_PemeriksaanFisik, 
            'A_Diagnosa' =>  $A_Diagnosa, 
            'P_RencanaTatalaksana' =>  $P_RencanaTatalaksana, 
            'P_InstruksiNonMedis' =>  $P_InstruksiNonMedis, 
            'NamaUser' =>  $NamaUser, 
            'GroupUser' =>  $GroupUser, 
            'SBAR' =>  $SBAR, 
            'Beratbadan' =>  $Beratbadan, 
            'TinggiBadan' =>  $TinggiBadan, 
            'Suhu' =>  $Suhu, 
            'FrekuensiNafas' =>  $FrekuensiNafas, 
            'TD_Sistol' => $TD_Sistol,
            'TD_Distol' => $TD_Distol,
            'FrekuensiNadi' => $FrekuensiNadi,
            'AlatBantu' => $AlatBantu,
            'Prothesa' => $Prothesa,
            'Cacat' => $Cacat
        ]);
    }

    public function CreateAssesmentRajalPerawat( $NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$NamaPasien,
                                            $KeluhanPasien, $S_Anamnesa,$S_RPD,
                                            $O_PemeriksaanFisik, $A_Diagnosa, $P_RencanaTatalaksana,$P_InstruksiNonMedis ,
                                            $NamaUser,$GroupUser,$SBAR,$Beratbadan,$TinggiBadan,$Suhu,$FrekuensiNafas
                                            ,$TD_Sistol,$TD_Distol,$FrekuensiNadi,$AlatBantu,$Prothesa,$Cacat
                                            )
    {        
        return  DB::connection('sqlsrv5')->table("EMR_RWJ_TTV")->insert([
            'NoMR' => $NoMR,
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' =>  $NoRegistrasi,
            'NoMR' => $NoMR,
            'Tgl' =>  $Tgl,
            'NamaPasien' => $NamaPasien, 
            'TD_Sistol' => $TD_Sistol,
            'TD_Distol' => $TD_Distol,
            'FrekuensiNadi' => $FrekuensiNadi,
            'Suhu' =>  $Suhu, 
            'FrekuensiNafas' =>  $FrekuensiNafas, 
            'Beratbadan' =>  $Beratbadan, 
            'TinggiBadan' =>  $TinggiBadan, 
            'LingkarKepala' => '-',
            'AlatBantu' => $AlatBantu,
            'Prothesa' => $Prothesa,
            'Cacat' => $Cacat,
            'CacatTubuh' => '-',
            'ADL' => 'Mandiri',
            'GCS_E' => '0',
            'GCS_M' => '0',
            'GCS_V' => '0',
            'GCS' => '0',
            'RiwayatPenyakit' =>  $S_RPD, 
            'KeluhanPasien' =>  $KeluhanPasien,
            'StatusPsikologis' => '-',
            'KeteranganNyeri' => '', 
            'GayaBerjalan' => '4',
            'MenggunakanAB' => '6',
            'HambatanEdukasi' => '5',
            'KEP_PTP' => '15',
            'S_Anamnesa' =>  $S_Anamnesa, 
            'P_RencanaTatalaksana' =>  $P_RencanaTatalaksana
        ]);
    }
    public function UpdateAssesmentRajalPerawat( $ID,$NoMR,$NoEpisode,$NoRegistrasi,$Tgl,$NamaPasien,
                                            $KeluhanPasien, $S_Anamnesa,$S_RPD,
                                            $O_PemeriksaanFisik, $A_Diagnosa, $P_RencanaTatalaksana,$P_InstruksiNonMedis ,
                                            $NamaUser,$GroupUser,$SBAR,$Beratbadan,$TinggiBadan,$Suhu,$FrekuensiNafas
                                            ,$TD_Sistol,$TD_Distol,$FrekuensiNadi,$AlatBantu,$Prothesa,$Cacat
                                            )
    {
        return  DB::connection('sqlsrv5')->table("EMR_RWJ_TTV")
        ->where('ID', $ID)
        ->update([
            'NoMR' => $NoMR,
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' =>  $NoRegistrasi,
            'NoMR' => $NoMR,
            'Tgl' =>  $Tgl,
            'NamaPasien' => $NamaPasien, 
            'TD_Sistol' => $TD_Sistol,
            'TD_Distol' => $TD_Distol,
            'FrekuensiNadi' => $FrekuensiNadi,
            'Suhu' =>  $Suhu, 
            'FrekuensiNafas' =>  $FrekuensiNafas, 
            'Beratbadan' =>  $Beratbadan, 
            'TinggiBadan' =>  $TinggiBadan, 
            'LingkarKepala' => '-',
            'AlatBantu' => $AlatBantu,
            'Prothesa' => $Prothesa,
            'Cacat' => $Cacat,
            'CacatTubuh' => '-',
            'ADL' => 'Mandiri',
            'GCS_E' => '0',
            'GCS_M' => '0',
            'GCS_V' => '0',
            'GCS' => '0',
            'RiwayatPenyakit' =>  $S_RPD, 
            'KeluhanPasien' =>  $KeluhanPasien,
            'StatusPsikologis' => '-',
            'KeteranganNyeri' => '', 
            'GayaBerjalan' => '4',
            'MenggunakanAB' => '6',
            'HambatanEdukasi' => '5',
            'KEP_PTP' => '15',
            'S_Anamnesa' =>  $S_Anamnesa, 
            'P_RencanaTatalaksana' =>  $P_RencanaTatalaksana
        ]);
    }
    public function getAssesment_Rajal_Perawat($NoRegistrasi){ 
        return  DB::connection('sqlsrv5')->table("EMR_RWJ_TTV")
        ->select('ID','NoMR',
        'NoEpisode',
        'NoRegistrasi',
        'NoMR',
        'Tgl',
        'NamaPasien',
        'TD_Sistol',
        'TD_Distol',
        'FrekuensiNadi' ,
        'Suhu',
        'FrekuensiNafas', 
        'Beratbadan',
        'TinggiBadan',
        'LingkarKepala',
        'AlatBantu',
        'Prothesa',
        'Cacat' ,
        'CacatTubuh',
        'ADL',
        'GCS_E',
        'GCS_M' ,
        'GCS_V',
        'GCS',
        'RiwayatPenyakit',
        'KeluhanPasien' ,
        'StatusPsikologis',
        'KeteranganNyeri',
        'GayaBerjalan',
        'MenggunakanAB',
        'HambatanEdukasi',
        'KEP_PTP',
        'S_Anamnesa',
        'P_RencanaTatalaksana'  ) 
        ->where('NoRegistrasi', $NoRegistrasi) 
        ->orderBy('ID','desc')
        ->get();
    }
    public function getCPPT($request)
    {
        return  DB::connection('sqlsrv5')->table("View_CPPT_New")   
        ->where('NoRegistrasi', $request->NoRegistrasi)
        ->orderBy('ID','desc')
        ->get();
    }
    public function ViewCpptPeriode($request)
    {
        return  DB::connection('sqlsrv5')->table("View_CPPT_New")   
        ->where('NoMR', $request->NoMR)
        ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), Tgl, 111), '/','-')"),[$request->PeriodeAwal, $request->PeriodeAkhir] )
        ->orderBy('ID','desc')
        ->get();
    }
    public function dataoperasiharian($request)
    {
        return  DB::connection('sqlsrv5')->table("EMR_OrderOperasi")   
        ->where(DB::raw("replace(CONVERT(VARCHAR(11),TglOperasi, 111), '/','-')"), $request->tanggal)
        ->where(DB::raw("SUBSTRING( convert(char(5), JamPelaksanaan, 108),1,2)"), $request->jam)  
        ->get();
    }
}