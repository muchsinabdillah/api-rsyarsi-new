<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class aDoctorRepositoryImpl implements aDoctorRepositoryInterface
{
    public function addDoctor($request)
    { 
        
    }
    public function editDoctor($request)
    {
         
    }
   
    public function getDoctorbyId($id) // akan dirubah
    {
        return  DB::connection('sqlsrv2')->table("View_Dokter")
        ->where('ID', $id)
        ->select('ID','NamaDokter','NamaUnit','CodeAntrian','foto','Pendidikan','Description','Pelatihan','IdLayanan','namaunit_spesialis','ID_Dokter_BPJS','NAMA_Dokter_BPJS')
        ->get();
    }
    public function getDoctorNonIGDbyId($id) // akan dirubah
    {
        return  DB::connection('sqlsrv2')->table("View_Dokter")
        ->where('ID', $id)
        ->where('NamaUnit','<>','IGD (Emergency)')  
        ->select('ID','NamaDokter','NamaUnit','CodeAntrian','foto','Pendidikan','Description','Pelatihan','IdLayanan','namaunit_spesialis','ID_Dokter_BPJS','NAMA_Dokter_BPJS')
        ->get();
    }
    public function getDoctorNonIGDbyIdDokterUnit($request) // akan dirubah
    {
        return  DB::connection('sqlsrv2')->table("View_Dokter")
        ->where('ID', $request->IdDokter)
        ->where('IdLayanan', $request->IdUnit)
        ->where('NamaUnit','<>','IGD (Emergency)')  
        ->select('ID','NamaDokter','NamaUnit','CodeAntrian','foto','Pendidikan','Description','Pelatihan','IdLayanan','namaunit_spesialis','ID_Dokter_BPJS','NAMA_Dokter_BPJS')
        ->get();
    }
    public function getDoctorbyUnit($id)
    {
        return  DB::connection('sqlsrv2')->table("View_Dokter")
        ->where('active', '1')
        ->where('IdLayanan',$id) 
        ->select('ID','NamaDokter','NamaUnit','foto','Pendidikan','Description','Pelatihan','IdLayanan','namaunit_spesialis')
        ->get();
    }
    public function getDoctorbyUnitAll()
    {
        return  DB::connection('sqlsrv2')->table("View_Dokter")
        ->where('active', '1')
        ->select('ID','NamaDokter','NamaUnit','foto','Pendidikan','Description','Pelatihan','IdLayanan','namaunit_spesialis')
        ->get();
    }
    public function getDoctorbyUnitAllTop() //data
    {
        return  DB::connection('sqlsrv2')->table("View_Dokter")
        ->where('active', '1')
        ->where('IdLayanan','<>', '1')
        ->where('IdLayanan','<>', '39')
        ->where('IdLayanan','<>', '9')
        ->where('IdLayanan','<>', '10')
        ->where('IdLayanan','<>', '47')  
        ->where('IdLayanan','<>', '53')  
        ->where('IdLayanan','<>', '55')  
        ->select('ID','NamaDokter','NamaUnit','foto','Pendidikan','Description','Pelatihan','IdLayanan','namaunit_spesialis')
        ->whereIn('ID',array('3813','3869','3873','3865','3862','3858','3859','3861','3857','3843','3860'))
        ->get();
    }
    public function getDoctorbyIDBPJS($id)
    {
        return  DB::connection('sqlsrv2')->table("Doctors")
        ->where('active', '1')
        ->where('ID_Dokter_BPJS',$id) 
        ->get();
    }
}
