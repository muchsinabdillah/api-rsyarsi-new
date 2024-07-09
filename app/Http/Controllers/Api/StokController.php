<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Service\aHnaService;
use App\Http\Service\aStokService;
use App\Http\Controllers\Controller;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;

class StokController extends Controller
{
    public function getStokBarangbyUnitNameLike(Request $request)
    {
         
        $aStokRepository = new aStokRepositoryImpl(); 
        $aMutasiService = new aStokService( 
            $aStokRepository 
        );
        $execute =  $aMutasiService->getStokBarangbyUnitNameLike($request);
        return $execute;
    }
    public function getStokBarangbyUnit(Request $request)
    {
         
        $aStokRepository = new aStokRepositoryImpl(); 
        $aMutasiService = new aStokService( 
            $aStokRepository 
        );
        $execute =  $aMutasiService->getStokBarangbyUnit($request);
        return $execute;
    }
    public function getBukuStokBarangbyUnit(Request $request)
    {
         
        $aStokRepository = new aStokRepositoryImpl(); 
        $aMutasiService = new aStokService( 
            $aStokRepository 
        );
        $execute =  $aMutasiService->getBukuStokBarangbyUnit($request);
        return $execute;
    }
    public function getBukuStokBarangBeforebyUnit(Request $request)
    {
         
        $aStokRepository = new aStokRepositoryImpl(); 
        $aMutasiService = new aStokService( 
            $aStokRepository 
        );
        $execute =  $aMutasiService->getBukuStokBarangBeforebyUnit($request);
        return $execute;
    }
    public function getHnabyKodeBarang(Request $request)
    {
          

        $aHnaRepository = new aHnaRepositoryImpl(); 
        $barangRepository = new aBarangRepositoryImpl(); 
        $aMutasiService = new aHnaService( 
            $aHnaRepository, 
            $barangRepository 
        );
        $execute =  $aMutasiService->getHnabyKodeBarang($request);
        return $execute;
    }
}
