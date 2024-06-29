<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class aJaminanRepositoryImpl implements aJaminanRepositoryInterface
{
   
    public function getJaminanAllAktif($idgroupjaminan)
    {

        if($idgroupjaminan == "1"){
            return  DB::connection('sqlsrv2')->table("MstrPerusahaanJPK")
            ->where('StatusAktif', $idgroupjaminan)
            ->where('NamaPerusahaan', 'UMUM')
            ->select('ID','NamaPerusahaan')
            ->get();
        }elseif($idgroupjaminan == "2"){
            return  DB::connection('sqlsrv2')->table("MstrPerusahaanAsuransi")
            ->where('StatusAktif', $idgroupjaminan)
            ->select('ID','NamaPerusahaan')
            ->get();
        }elseif($idgroupjaminan == "5"){
            return  DB::connection('sqlsrv2')->table("MstrPerusahaanJPK")
            ->where('StatusAktif', $idgroupjaminan)
            ->select('ID','NamaPerusahaan')
            ->get();
        }
        
    }
    public function getJaminanAllAktifbyId($idgroupjaminan,$idjaminan)
    {
        if($idgroupjaminan == "1"){
            return  DB::connection('sqlsrv2')->table("MstrPerusahaanJPK")
            ->where('StatusAktif', $idgroupjaminan)
            ->where('ID', $idjaminan)
            ->where('NamaPerusahaan', 'UMUM')
            //->select('ID','NamaPerusahaan')
            ->get();
        }elseif($idgroupjaminan == "2"){
            return  DB::connection('sqlsrv2')->table("MstrPerusahaanAsuransi")
            ->where('StatusAktif', $idgroupjaminan)
            ->where('ID', $idjaminan)
           // ->select('ID','NamaPerusahaan')
            ->get();
        }elseif($idgroupjaminan == "5"){
            return  DB::connection('sqlsrv2')->table("MstrPerusahaanJPK")
            ->where('StatusAktif', $idgroupjaminan)
            ->where('ID', $idjaminan)
            //->select('ID','NamaPerusahaan')
            ->get();
        }
        
    }
}