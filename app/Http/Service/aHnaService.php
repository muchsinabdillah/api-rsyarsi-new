<?php

namespace App\Http\Service;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\HargaJualTrait;
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;

class aHnaService extends Controller
{
    use HargaJualTrait;
    private $aHnaRepository;
    private $barangRepository;
    public function __construct( 
        aHnaRepositoryImpl $aHnaRepository,
        aBarangRepositoryImpl $barangRepository
    ) { 
        $this->aHnaRepository = $aHnaRepository;
        $this->barangRepository = $barangRepository;
    }

    public function getHnabyKodeBarang(Request $request)
    {
        // validate 
        $request->validate([
            "ProductCode" => "required",             
            "NoRegistrasi" => "required",             
            "GroupJaminanx" => "required",           
            "Kelasid" => "required",           
            "tgl" => "required" 
        ]);
        try {
             
            $golongan = $this->barangRepository->getBarangbyIdandgolongan($request->ProductCode);
            $datagolongan = $golongan->first();
          
            $hna = $this->aHnaRepository->getHnaHighPeriodik($request->ProductCode,date('Y-m-d'));
 
// return $request->GroupJaminan,$request->NoRegistrasi,$datahna->NominalHna,$datagolongan,$request->Kelasid
                if($hna->count() < 1 ){
                    $harga = 0;
                }else{
                    $datahna = $hna->first()->first();
                    
                    $harga = $this->HargaJual($request->GroupJaminanx,$request->NoRegistrasi,$datahna->NominalHna,$datagolongan,$request->Kelasid);

                    
                }

               return $this->sendResponse($harga, "Data Product ditemukan.");
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
}