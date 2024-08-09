<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class aUnitRepositoryImpl implements aUnitRepositoryInterface
{
    public function addUnit($request)
    { 
        
    }
    public function editUnit($request)
    {
         
    }
   
    public function getUnitPoliklinikbyId($id)
    {
        return  DB::connection('sqlsrv2')->table("MstrUnitPerwatan")
        ->where('ID', $id)
        ->select('ID','NamaUnit','codeBPJS','CodeSubBPJS','NamaBPJS')
        ->get();
    }
  
    public function getUnitPoliklinik()
    {
        return  DB::connection('sqlsrv2')->table("MstrUnitPerwatan")
        ->where('grup_instalasi', 'RAWAT JALAN')
        ->where('active', '1')
        ->select('ID','NamaUnit','Foto')
        ->get();
    }
    public function getUnit()
    {
        return  DB::connection('sqlsrv2')->table("MstrUnitPerwatan") 
        ->select('ID','NamaUnit','Foto')
        ->get();
    }
    public function getUnitbyId($id)
    {
        return  DB::connection('sqlsrv2')->table("MstrUnitPerwatan")
        ->where('ID', $id)
        ->select('ID','NamaUnit','codeBPJS','CodeSubBPJS','NamaBPJS')
        ->get();
    }
}
