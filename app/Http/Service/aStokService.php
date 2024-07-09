<?php

namespace App\Http\Service;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;  
use App\Http\Repository\aStokRepositoryImpl;

class aStokService extends Controller
{
    private $aStokRepository;
    public function __construct( 
        aStokRepositoryImpl $aStokRepository
    ) { 
        $this->aStokRepository = $aStokRepository;
    }
    public function getStokBarangbyUnitNameLike(Request $request)
    {
        // validate 
        $request->validate([
            "unit" => "required",
            "name" => "required" 
        ]);
        try {
            $count = $this->aStokRepository->getStokBarangbyUnitNameLike($request);
            if($count->count() > 0){ 
                return $this->sendResponse($count, "Data Product ditemukan.");
            }else{
                return $this->sendError("Data Product Found.", [], 400);
            }
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function getStokBarangbyUnit(Request $request)
    {
        // validate 
        $request->validate([
            "unit" => "required" 
        ]);
        try {
            $count = $this->aStokRepository->getStokBarangbyUnit($request);
            if($count->count() > 0){ 
                return $this->sendResponse($count, "Data Product ditemukan.");
            }else{
                return $this->sendError("Data Product Found.", [], 400);
            }
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function getBukuStokBarangbyUnit(Request $request)
    {
        // validate 
        $request->validate([
            "PeriodeAwal" => "required",             
            "PeriodeAkhir" => "required", 
            "unit" => "required",             
            "ProductCode" => "required" 


        ]);
        try {
            $count = $this->aStokRepository->getBukuStokBarangbyUnit($request);
   
                return $this->sendResponse($count, "Data Product ditemukan.");
            
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function getBukuStokBarangBeforebyUnit(Request $request)
    {
        // validate 
        $request->validate([
            "PeriodeAwal" => "required",             
            "PeriodeAkhir" => "required", 
            "unit" => "required",             
            "ProductCode" => "required" 


        ]);
        try {
            $count = $this->aStokRepository->getBukuStokBarangBeforebyUnit($request);
            if($count->count() > 0){ 
                return $this->sendResponse($count, "Data Product ditemukan.");
            }else{
                return $this->sendError("Data Product Found.", [], 400);
            }
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    
}