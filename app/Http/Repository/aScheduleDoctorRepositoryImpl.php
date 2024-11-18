<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use App\Http\Repository\aScheduleDoctorRepositoryInterface;
use GuzzleHttp\Psr7\Request;

class aScheduleDoctorRepositoryImpl implements aScheduleDoctorRepositoryInterface
{
 
    public function getScheduleDoctorSenin($request)
    {
        if($request->IdDokter == ""){ 
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Senin_Awal  AS JamAwal'),
            DB::raw('Senin_Akhir  AS JamAkhir'),
            DB::raw('Senin_Sesion  AS NamaSesion') )
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Senin', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Senin_Awal  AS JamAwal'),
            DB::raw('Senin_Akhir  AS JamAkhir'),
            DB::raw('Senin_Sesion  AS NamaSesion') )
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('IdDokter', $request->IdDokter)
            ->where('Senin', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }
       
    }
    public function getScheduleDoctorSelasa($request)
    {
        if($request->IdDokter == ""){ 
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Selasa_Awal  AS JamAwal'),
            DB::raw('Selasa_Akhir  AS JamAkhir'),
            DB::raw('Selasa_Sesion  AS NamaSesion') ) 
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Selasa', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Selasa_Awal  AS JamAwal'),
            DB::raw('Selasa_Akhir  AS JamAkhir'),
            DB::raw('Selasa_Sesion  AS NamaSesion') )  
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('IdDokter', $request->IdDokter)
            ->where('Selasa', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }
       
    }
    public function getScheduleDoctorRabu($request)
    {
        if($request->IdDokter == ""){ 
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Rabu_Awal  AS JamAwal'),
            DB::raw('Rabu_Akhir  AS JamAkhir'),
            DB::raw('Rabu_Sesion  AS NamaSesion') )   
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Rabu', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Rabu_Awal  AS JamAwal'),
            DB::raw('Rabu_Akhir  AS JamAkhir'),
            DB::raw('Rabu_Sesion  AS NamaSesion') )    
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('IdDokter', $request->IdDokter)
            ->where('Rabu', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }
        
    }
    public function getScheduleDoctorKamis($request)
    {
        if($request->IdDokter == ""){ 
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli' ,
            DB::raw('Kamis_Awal  AS JamAwal'),
            DB::raw('Kamis_akhir  AS JamAkhir'),
            DB::raw('Kamis_Sesion  AS NamaSesion') )     
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Kamis', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli' ,
            DB::raw('Kamis_Awal  AS JamAwal'),
            DB::raw('Kamis_akhir  AS JamAkhir'),
            DB::raw('Kamis_Sesion  AS NamaSesion') )     
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('IdDokter', $request->IdDokter)
            ->where('Kamis', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }
       
    }
    public function getScheduleDoctorJumat($request)
    {
        if($request->IdDokter == ""){ 
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Jumat_Awal  AS JamAwal'),
            DB::raw('Jumat_Akhir  AS JamAkhir'),
            DB::raw('Jumat_Sesion  AS NamaSesion') )      
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Jumat', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Jumat_Awal  AS JamAwal'),
            DB::raw('Jumat_Akhir  AS JamAkhir'),
            DB::raw('Jumat_Sesion  AS NamaSesion') )       
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Jumat', '1')
            ->where('IdDokter', $request->IdDokter)
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }
       
    }
    public function getScheduleDoctorSabtu($request)
    {
        if($request->IdDokter == ""){ 
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Sabtu_Awal  AS JamAwal'),
            DB::raw('Sabtu_Akhir  AS JamAkhir'),
            DB::raw('Sabtu_Sesion  AS NamaSesion') )       
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Sabtu', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli',
            DB::raw('Sabtu_Awal  AS JamAwal'),
            DB::raw('Sabtu_Akhir  AS JamAkhir'),
            DB::raw('Sabtu_Sesion  AS NamaSesion') )       
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('IdDokter', $request->IdDokter)
            ->where('Sabtu', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }
        
    }
    public function getScheduleDoctorMinggu($request)
    {
        if($request->IdDokter == ""){ 
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli' ,
            DB::raw('Minggu_Awal  AS JamAwal'),
            DB::raw('Minggu_Akhir  AS JamAkhir'),
            DB::raw('Minggu_Sesion  AS NamaSesion') )        
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('Minggu', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
            ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli' ,
            DB::raw('Minggu_Awal  AS JamAwal'),
            DB::raw('Minggu_Akhir  AS JamAkhir'),
            DB::raw('Minggu_Sesion  AS NamaSesion') )        
            ->where('GroupPerawatan', $request->IdUnit)
            ->where('IdDokter', $request->IdDokter)
            ->where('Minggu', '1')
            ->orderByDesc('IdDokter')
            ->orderByDesc('GroupJadwal')
            ->get();
        }
       
    }
   
    public function getScheduleDoctorAll()
    {
        return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
        ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli'
        ,'Senin_Awal','Senin_Akhir' ,'Senin_Sesion'
        ,'Selasa_Awal','Selasa_Akhir' ,'Selasa_Sesion'
        ,'Rabu_Awal','Rabu_Akhir' ,'Rabu_Sesion'
        ,'Kamis_Awal','Kamis_akhir' ,'Kamis_Sesion'
        ,'Jumat_Awal','Jumat_Akhir' ,'Jumat_Sesion'
        ,'Sabtu_Awal','Sabtu_Akhir' ,'Sabtu_Sesion'
        ,'Minggu_Awal','Minggu_Akhir' ,'Minggu_Sesion')
        ->where('Senin', '1')
        ->where('Selasa', '1')
        ->where('Rabu', '1')
        ->where('Kamis', '1')
        ->where('Jumat', '1')
        ->where('Sabtu', '1')
        ->where('Minggu', '1') 
        ->orderByDesc('IdDokter')
        ->orderByDesc('GroupJadwal')
        ->get();
    }
    public function getScheduleDoctorDetilbyId($IdDokter)
    {
        return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
        ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli'
        ,'Senin_Awal','Senin_Akhir' ,'Senin_Sesion'
        ,'Selasa_Awal','Selasa_Akhir' ,'Selasa_Sesion'
        ,'Rabu_Awal','Rabu_Akhir' ,'Rabu_Sesion'
        ,'Kamis_Awal','Kamis_akhir' ,'Kamis_Sesion'
        ,'Jumat_Awal','Jumat_Akhir' ,'Jumat_Sesion'
        ,'Sabtu_Awal','Sabtu_Akhir' ,'Sabtu_Sesion'
        ,'Minggu_Awal','Minggu_Akhir' ,'Minggu_Sesion')
        ->where('IdDokter',$IdDokter)  
        ->orderByDesc('IdDokter')
        ->orderByDesc('GroupJadwal')
        ->get();
    }
    public function getScheduleDoctorDetilNonBPJSbyId($IdDokter)
    {
        return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
        ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli'
        ,'Senin_Awal','Senin_Akhir' ,'Senin_Sesion'
        ,'Selasa_Awal','Selasa_Akhir' ,'Selasa_Sesion'
        ,'Rabu_Awal','Rabu_Akhir' ,'Rabu_Sesion'
        ,'Kamis_Awal','Kamis_akhir' ,'Kamis_Sesion'
        ,'Jumat_Awal','Jumat_Akhir' ,'Jumat_Sesion'
        ,'Sabtu_Awal','Sabtu_Akhir' ,'Sabtu_Sesion'
        ,'Minggu_Awal','Minggu_Akhir' ,'Minggu_Sesion')
        ->where('IdDokter',$IdDokter)  
        ->where('Poli','<>','IGD (Emergency)')  
        ->where('Poli','<>','Laboratorium')  
        ->orderByDesc('IdDokter')
        ->orderByDesc('GroupJadwal')
        ->get();
    }
    public function getScheduleDoctorbyIdDoctorUnit($request)
    {
        return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
        ->select('IdDokter','IdJadwalDokter','GroupJadwal','First_Name','Poli'
        ,'Senin_Awal','Senin_Akhir' ,'Senin_Sesion'
        ,'Selasa_Awal','Selasa_Akhir' ,'Selasa_Sesion'
        ,'Rabu_Awal','Rabu_Akhir' ,'Rabu_Sesion'
        ,'Kamis_Awal','Kamis_akhir' ,'Kamis_Sesion'
        ,'Jumat_Awal','Jumat_Akhir' ,'Jumat_Sesion'
        ,'Sabtu_Awal','Sabtu_Akhir' ,'Sabtu_Sesion'
        ,'Minggu_Awal','Minggu_Akhir' ,'Minggu_Sesion')
        ->where('IdDokter',$request->IdDokter)  
        ->where('IDUnit',$request->IdUnit)  
        ->where('Poli','<>','IGD (Emergency)')  
        ->where('Poli','<>','Laboratorium')  
        ->orderByDesc('IdDokter')
        ->orderByDesc('GroupJadwal')
        ->get();
    }
  
    public function getScheduleDoctorbyIdDoctor($id)
    {
        return  DB::connection('sqlsrv2')->table("View_ScheduleDokter")
        ->where('active', '1')
        ->where('IdLayanan',$id) 
        ->select('ID','NamaDokter','NamaUnit')
        ->get();
    }
    public function getCutiDokter($IdDokter,$tglbooking)
    {
        return  DB::connection('sqlsrv2')->table("TR_CUTI_DOKTER")
        ->where('Batal', '0')
        ->where('id_dokter',$IdDokter) 
        ->whereRaw("'$tglbooking' BETWEEN periode_awal AND periode_akhir")
        ->get();
    }
    /// untuk reservasi
    public function getScheduleDoctorForTRSSenin($IdDokter,$IdGrupPerawatan,$jampraktek,$groupjadwal)
    {
        if($jampraktek == ""){ 
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Senin_Sesion  AS NamaSesion') ,
                        DB::raw('Senin_Max  AS MaxKuota') ,
                        DB::raw('Senin_Max_JKN  AS Max_JKN') ,
                        DB::raw('Senin_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Senin_Awal  AS JamAwal') ,
                        DB::raw('Senin_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Senin  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Senin', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Senin_Sesion  AS NamaSesion') ,
                        DB::raw('Senin_Max  AS MaxKuota') ,
                        DB::raw('Senin_Max_JKN  AS Max_JKN') ,
                        DB::raw('Senin_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Senin_Awal  AS JamAwal') ,
                        DB::raw('Senin_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Senin  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Senin', '1')
            ->where('Status_Aktif', '1')
            ->where('Senin_Waktu', $jampraktek)
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }
           
    }
    public function getScheduleDoctorForTRSMinggu($IdDokter,$IdGrupPerawatan,$jampraktek,$groupjadwal)
    {
        if($jampraktek == ""){ 
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Minggu_Sesion  AS NamaSesion') ,
                        DB::raw('Minggu_Max  AS MaxKuota') ,
                        DB::raw('Minggu_Max_JKN  AS Max_JKN') ,
                        DB::raw('Minggu_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Minggu_Awal  AS JamAwal') ,
                        DB::raw('Minggu_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Minggu  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Minggu', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Minggu_Sesion  AS NamaSesion') ,
                        DB::raw('Minggu_Max  AS MaxKuota') ,
                        DB::raw('Minggu_Max_JKN  AS Max_JKN') ,
                        DB::raw('Minggu_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Minggu_Awal  AS JamAwal') ,
                        DB::raw('Minggu_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Minggu  AS Close_Schedules'),   )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Minggu', '1')
            ->where('Status_Aktif', '1')
            ->where('Minggu_Waktu', $jampraktek)
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }
            
    }

    public function getScheduleDoctorForTRSSelasa($IdDokter,$IdGrupPerawatan,$jampraktek,$groupjadwal)
    {
        if($jampraktek == ""){ 
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Selasa_Sesion  AS NamaSesion') ,
                        DB::raw('Selasa_Max  AS MaxKuota') ,
                        DB::raw('Selasa_Max_JKN  AS Max_JKN') ,
                        DB::raw('Selasa_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Selasa_Awal  AS JamAwal') ,
                        DB::raw('Selasa_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Selasa  AS Close_Schedules'),   )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Selasa', '1')
            ->where('Status_Aktif', '1') 
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Selasa_Sesion  AS NamaSesion') ,
                        DB::raw('Selasa_Max  AS MaxKuota') ,
                        DB::raw('Selasa_Max_JKN  AS Max_JKN') ,
                        DB::raw('Selasa_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Selasa_Awal  AS JamAwal') ,
                        DB::raw('Selasa_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Selasa  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Selasa', '1')
            ->where('Status_Aktif', '1')
            ->where('Selasa_Waktu', $jampraktek)
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        } 
    }
    public function getScheduleDoctorForTRSRabu($IdDokter,$IdGrupPerawatan,$jampraktek,$groupjadwal)
    {
        if($jampraktek == ""){ 
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Rabu_Sesion  AS NamaSesion') ,
                        DB::raw('Rabu_Max  AS MaxKuota') ,
                        DB::raw('Rabu_Max_JKN  AS Max_JKN') ,
                        DB::raw('Rabu_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Rabu_Awal  AS JamAwal') ,
                        DB::raw('Rabu_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Rabu  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Rabu', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Rabu_Sesion  AS NamaSesion') ,
                        DB::raw('Rabu_Max  AS MaxKuota') ,
                        DB::raw('Rabu_Max_JKN  AS Max_JKN') ,
                        DB::raw('Rabu_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Rabu_Awal  AS JamAwal') ,
                        DB::raw('Rabu_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Rabu  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Rabu', '1')
            ->where('Status_Aktif', '1')
            ->where('Rabu_Waktu', $jampraktek)
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }
            
    }
    public function getScheduleDoctorForTRSKamis($IdDokter,$IdGrupPerawatan,$jampraktek,$groupjadwal)
    {
        if($jampraktek == ""){ 
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Kamis_Sesion  AS NamaSesion') ,
                        DB::raw('Kamis_Max  AS MaxKuota') ,
                        DB::raw('Kamis_Max_JKN  AS Max_JKN') ,
                        DB::raw('Kamis_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Kamis_Awal  AS JamAwal') ,
                        DB::raw('Kamis_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Kamis  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Kamis', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Kamis_Sesion  AS NamaSesion') ,
                        DB::raw('Kamis_Max  AS MaxKuota') ,
                        DB::raw('Kamis_Max_JKN  AS Max_JKN') ,
                        DB::raw('Kamis_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Kamis_Awal  AS JamAwal') ,
                        DB::raw('Kamis_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Kamis  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Kamis', '1')
            ->where('Status_Aktif', '1')
            ->where('Kamis_Waktu', $jampraktek)
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }
            
    }
    public function getScheduleDoctorForTRSJumat($IdDokter,$IdGrupPerawatan,$jampraktek,$groupjadwal)
    {
        if($jampraktek == ""){ 
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Jumat_Sesion  AS NamaSesion') ,
                        DB::raw('Jumat_Max  AS MaxKuota') ,
                        DB::raw('Jumat_Max_JKN  AS Max_JKN') ,
                        DB::raw('Jumat_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Jumat_Awal  AS JamAwal') ,
                        DB::raw('Jumat_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Jumat  AS Close_Schedules'),  )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Jumat', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Jumat_Sesion  AS NamaSesion') ,
                        DB::raw('Jumat_Max  AS MaxKuota') ,
                        DB::raw('Jumat_Max_JKN  AS Max_JKN') ,
                        DB::raw('Jumat_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Jumat_Awal  AS JamAwal') ,
                        DB::raw('Jumat_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Jumat  AS Close_Schedules'),   )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Jumat', '1')
            ->where('Status_Aktif', '1')
            ->where('Jumat_Waktu', $jampraktek)
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }
           
    }
    public function getScheduleDoctorForTRSSabtu($IdDokter,$IdGrupPerawatan,$jampraktek,$groupjadwal)
    {
        if($jampraktek == ""){ 
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Sabtu_Sesion  AS NamaSesion') ,
                        DB::raw('Sabtu_Max  AS MaxKuota') ,
                        DB::raw('Sabtu_Max_JKN  AS Max_JKN') ,
                        DB::raw('Sabtu_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Sabtu_Awal  AS JamAwal') ,
                        DB::raw('Sabtu_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Sabtu  AS Close_Schedules'),   )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Sabtu', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }else{
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('ID',
                        DB::raw('Sabtu_Sesion  AS NamaSesion') ,
                        DB::raw('Sabtu_Max  AS MaxKuota') ,
                        DB::raw('Sabtu_Max_JKN  AS Max_JKN') ,
                        DB::raw('Sabtu_Max_NonJKN  AS Max_NonJKN') ,
                        DB::raw('Sabtu_Awal  AS JamAwal') ,
                        DB::raw('Sabtu_Akhir  AS JamAkhir'), 
                        DB::raw('Close_Schedule_Sabtu  AS Close_Schedules'),   )
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('IDDokter', $IdDokter)
            ->where('Sabtu', '1')
            ->where('Status_Aktif', '1')
            ->where('Sabtu_Waktu', $jampraktek)
            ->where('Group_Jadwal', $groupjadwal)
            ->orderByDesc('IdDokter')
            ->orderByDesc('Group_Jadwal')
            ->get();
        }
            
    }


    // untuk cari data dokter di hari itu . nama dokter aajaa
    public function getScheduleGroubyDoctorForTRSSenin($IdGrupPerawatan,$groupjadwal)
    {
       
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->join('Doctors', 'Doctors.ID', '=', 'JadwalPraktek.IDDokter')
            ->where('IDUnit', $IdGrupPerawatan) 
            ->where('Senin', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->groupBy('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->get();
       
           
    }
    public function getScheduleGroubyDoctorForTRSMinggu($IdGrupPerawatan,$groupjadwal)
    {
        
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->join('Doctors', 'Doctors.ID', '=', 'JadwalPraktek.IDDokter')
            ->where('IDUnit', $IdGrupPerawatan) 
            ->where('Minggu', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->groupBy('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->get();
     
            
    }

    public function getScheduleGroubyDoctorForTRSSelasa($IdGrupPerawatan,$groupjadwal)
    {
        
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->join('Doctors', 'Doctors.ID', '=', 'JadwalPraktek.IDDokter')
            ->where('IDUnit', $IdGrupPerawatan)
            ->where('Selasa', '1')
            ->where('Status_Aktif', '1') 
            ->where('Group_Jadwal', $groupjadwal)
            ->groupBy('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->get();
        
    }
    public function getScheduleGroubyDoctorForTRSRabu($IdGrupPerawatan,$groupjadwal)
    {
        
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->join('Doctors', 'Doctors.ID', '=', 'JadwalPraktek.IDDokter')
            ->where('IDUnit', $IdGrupPerawatan) 
            ->where('Rabu', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->groupBy('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->get();
         
            
    }
    public function getScheduleGroubyDoctorForTRSKamis($IdGrupPerawatan,$groupjadwal)
    {
       
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->join('Doctors', 'Doctors.ID', '=', 'JadwalPraktek.IDDokter')
            ->where('IDUnit', $IdGrupPerawatan) 
            ->where('Kamis', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->groupBy('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->get();
         
            
    }
    public function getScheduleGroubyDoctorForTRSJumat($IdGrupPerawatan,$groupjadwal)
    {
       
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->join('Doctors', 'Doctors.ID', '=', 'JadwalPraktek.IDDokter')
            ->where('IDUnit', $IdGrupPerawatan) 
            ->where('Jumat', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->groupBy('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->get();
         
           
    }
    public function getScheduleGroubyDoctorForTRSSabtu($IdGrupPerawatan,$groupjadwal)
    {
        
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->join('Doctors', 'Doctors.ID', '=', 'JadwalPraktek.IDDokter')
            ->where('IDUnit', $IdGrupPerawatan) 
            ->where('Sabtu', '1') 
            ->where('Status_Aktif', '1')
            ->where('Group_Jadwal', $groupjadwal)
            ->groupBy('JadwalPraktek.IDDokter','JadwalPraktek.NamaDokter','Doctors.foto')
            ->get();
    }
    public function getScheduleDoctorbyIdJadwalDoctor($IdJadwalDokter)
    {
        
            return  DB::connection('sqlsrv2')->table("JadwalPraktek")
            ->select('Senin_Waktu','Selasa_Waktu','Rabu_Waktu',
            'Kamis_Waktu','Jumat_Waktu','Sabtu_Waktu','Minggu_Waktu',
            'Senin_Max_NonJKN','Senin_Max_JKN','Selasa_Max_JKN','Selasa_Max_NonJKN','Rabu_Max_JKN','Rabu_Max_NonJKN',
            'Kamis_Max_JKN','Kamis_Max_NonJKN','Jumat_Max_JKN','Jumat_Max_NonJKN','Sabtu_Max_JKN','Sabtu_Max_NonJKN',
            'Minggu_Max_JKN','Minggu_Max_NonJKN') 
            ->where('ID', $IdJadwalDokter)  
            ->get();
    }
    
}
