<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class bHasilMCURepositoryImpl implements bHasilMCURepositoryInterface
{ 
    public function uploaPdfMedicalCheckupbyKodeJenis($request)
    {
        if($request->KelompokHasil == "1"){
            $namadocument = "HASILMCU";
        }elseif($request->KelompokHasil == "2"){
            $namadocument = "LABORATORIUM_";
        }elseif($request->KelompokHasil == "3"){
            $namadocument = "RADIOLOGI";
        }elseif($request->KelompokHasil == "4"){
            $namadocument = "EKG";
        }elseif($request->KelompokHasil == "5"){
            $namadocument = "TREADMILL";
        }elseif($request->KelompokHasil == "6"){
            $namadocument = "AUDIOMETRI";
        }elseif($request->KelompokHasil == "7"){
            $namadocument = "SPIROMETRI";
        }elseif($request->KelompokHasil == "8"){
            $namadocument = "SKBN";
        }elseif($request->KelompokHasil == "9"){
            $namadocument = "SKKJ";
        }

        return  DB::connection('sqlsrv6')->table("T_Hasil_MCU_PDF")->insert([
            'KelompokHasil' => $namadocument,
            'NoUrut' => $request->KelompokHasil,
            'Url_Pdf_Local' =>  $request->Url_Pdf_Local,
            'NoRegistrasi' =>  $request->NoRegistrasi
        ]);
    }
    public function uploaPdfHasilMCUFinish($request)
    {
        return  DB::connection('sqlsrv6')->table("T_HASIL_MCU_PDF_HDR")->insert([
            'NoRegistrasi' => $request->NoRegistrasi,
            'URL_PDF' =>  $request->Url_Pdf_Local,
        ]);
    }
    public function ResetuploaPdfHasilMCUFinish($request)
    {
        return  DB::connection('sqlsrv6')->table("T_Hasil_MCU_PDF")
        ->where('NoRegistrasi',$request->NoRegistrasi) 
        ->delete(); 
    }
    public function hasilMCU($reg)
    {
        return  DB::connection('sqlsrv5')->table("View_TempMCU1")
        ->where('noregistrasi',$reg)
        ->get();
    }
    public function hasilMCU2($reg)
    {
        return  DB::connection('sqlsrv5')->table("View_TempMCU2")
        ->select(  'ID', 'NoMR', 'NoEpisode', 'NoRegistrasi'  ,'Tanggal', 'KandungKemih', 'ketKandungKemih', 'Anus', 'ketAnus', 'GenitaliaEksternal', 'ketGenitaliaEksternal', 'Prostat', 'ketProstat', 'Vertebra', 'Ketvertebra', 'Ekstra_SimetriKa'
        ,'Ekstra_SimetriKi', 'Ekstra_GerakanKa', 'Ekstra_GerakanKi', 'Ekstra_RoMKa', 'Ekstra_RoMKi', 'Ekstra_AbduksiNTKa', 'Ekstra_AbduksiNTKi', 'Ekstra_AbduksiHTKa', 'Ekstra_AbduksiHTKi', 'Ekstra_DropArmTKa'
        ,'Ekstra_DropArmTKi', 'Ekstra_YergasonKa', 'Ekstra_YergasonKi', 'Ekstra_SpeedTesKa', 'Ekstra_SpeedTesKi', 'Ekstra_TulangKa', 'Ekstra_TulangKi', 'Ekstra_SensibilitasKa', 'Ekstra_SensibilitasKi', 'Ekstra_OedemaKa'
        ,  'Ekstra_OedemaKi', 'Ekstra_VarisesKa', 'Ekstra_VarisesKi', 'Ekstra_KOtotKa', 'Ekstra_KOtotKi', 'Ekstra_KOPPTKa', 'Ekstra_KOPPTKi', 'Ekstra_KOPhalTKa', 'Ekstra_KOPhalTKi', 'Ekstra_KOTinnTKa', 'Ekstra_KOTinnTKi'
        , 'Ekstra_KOFinsTKa', 'Ekstra_KOFinsTKi', 'Ekstra_VaskularisasiKa', 'Ekstra_VaskularisasiKi', 'Ekstra_KelKukuKa', 'Ekstra_KelKukuKi', 'Ekstra_Keterangan', 'EkstrB_SimetriKa', 'EkstrB_SimetriKi', 'EkstrB_GerakanKa'
        , 'EkstrB_GerakanKi', 'EkstrB_TLasequeKa', 'EkstrB_TLasequeKi', 'EkstrB_TKerniqueKa', 'EkstrB_TKerniqueKi', 'EkstrB_TPatrickKa', 'EkstrB_TPatrickKi', 'EkstrB_TKontraKa', 'EkstrB_TKontraKi', 'EkstrB_NyeriTKa'
        , 'EkstrB_NyeriTKi', 'EkstrB_KOKa', 'EkstrB_KOKi', 'EkstrB_TulangKa', 'EkstrB_TulangKi', 'EkstrB_SensibilitasKa', 'EkstrB_SensibilitasKi', 'EkstrB_OedemaKa', 'EkstrB_OedemaKi', 'EkstrB_VarisesKa', 'EkstrB_VarisesKi'
        , 'EkstrB_VaskularKa', 'EkstrB_VaskularKi', 'EkstrB_KelKukuKa', 'EkstrB_KelKukuKi', 'EkstrB_Keterangan', 'Omot_TrofiKa', 'Omot_TrofiKi', 'Omot_TonusKa', 'Omot_TonusKi', 'Omot_GerAbnoKa', 'Omot_GerAbnoKi'
        , 'Omot_Keterangan', 'FuSen_FSensorikKa', 'FuSen_FSensorikKi', 'FuSen_FotonomKa', 'FuSen_FotonomKi', 'FuSen_Keterangan', 'SFL_DISegera', 'SFL_DIJPendek', 'SFL_DIJMenengah', 'SFL_DIJPanjang'
        , 'SFL_KSON11', 'SFL_KSON12', 'SFL_KSOKeterangan', 'RFL_FisiologisKa', 'RFL_FisiologisKi', 'RFL_PatellaKa', 'RFL_PatellaKi', 'RFL_Lainnya', 'RFL_PBKa', 'RFL_PBKi', 'RFL_PBLainnya', 'Kulit', 'SelaputLendir', 'KulitLainnya',
        'SFL_OrientasiWaktu', 'SFL_OrientasiTempat', 'SFL_OrientasiOrang', 'SFL_KSON1', 'SFL_KSON2', 'SFL_KSON3', 'SFL_KSON4', 'SFL_KSON5', 'SFL_KSON6', 'SFL_KSON7', 'SFL_KSON8', 'SFL_KSON9', 'SFL_KSON10'
        , 'ResumeKelainan', 'HasilBodyMap', 'DiagnosaKerja', 'DiagnosisDiferensial', 'KetKesehatan', 'Saran', 'drPemeriksa', 'PFK_Radiologi', 'PFK_USG', 'PFK_EKG', 'PFK_EMG', 'PFK_Spirometri', 'PFK_Audiometri'
        , 'PFK_Treadmill', 'PFK_Echo', 'PFK_Lab')
         ->where('noregistrasi',$reg)
        ->get();
    }
    public function listDocumentMCU($reg)
    {
        return  DB::connection('sqlsrv6')->table("T_HASIL_MCU_PDF")
        ->where('noregistrasi',$reg)
        ->orderBy('NoUrut', 'ASC')
        ->orderBy('ID', 'ASC')
        ->get();
    }
    public function listReportPDFMCU($request)
    {
        return  DB::connection('sqlsrv5')->table("View_ReportPdfHasilMCU")
        ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), [Visit Date], 111), '/','-')"),
        [$request->tglPeriodeBerobatAwal,$request->tglPeriodeBerobatAkhir])  
        ->orderBy('ID', 'DESC')
        ->get();
    }
    public function hasilMCUTreadmill($reg)
    {
        return  DB::connection('sqlsrv5')->table("MR_MCU_HASIL_TREADMILL")
        ->where('NoRegistrasi',$reg)
        ->get();
    }
    public function hasilMCUJiwa($reg)
    {
        return  DB::connection('sqlsrv5')->table("MR_MCU_SJIWA")
        ->where('NoRegistrasi',$reg)
        ->get();
    }
    public function hasilMCUBebasNarkoba($reg)
    {
        return  DB::connection('sqlsrv5')->table("MR_MCU_HASIL_NARKOBA")
        ->where('NoRegistrasi',$reg)
        ->get();
    }
    public function showKonsulDokterMCU($reg)
    {
        return  DB::connection('sqlsrv5')->table("MR_PemeriksaanMCU")
        ->select(  'AliasDokter', 'NamaDokter')
         ->where('NoRegistrasi',$reg)
         ->where('ShowCetakanKonsul','1')
        ->get();
    }
    
}