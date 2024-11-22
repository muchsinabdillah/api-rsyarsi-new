<?php

namespace App\Http\Service;

use Exception;
use App\Traits\FifoTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aAdjusmentRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;

class aAdjusmentService extends Controller
{
    use AutoNumberTrait;
    use FifoTrait;
    private $aDeliveryOrder;
    private $aBarangRepository; 
    private $aPurchaseOrderRepository;
    private $aBukuStok;
    private $aStokRepository;
    private $aHna; 
    private $aJurnal;
    private $aConsumableRepository;
    private $aMasterUnitRepository;
    private $ahnaRepository;
    private $adjusmentRepository;

    public function __construct(
        aBarangRepositoryImpl $aBarangRepository, 
        aStokRepositoryImpl $aStokRepository,
        aDeliveryOrderRepositoryImpl $aDeliveryOrder,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aHnaRepositoryImpl $ahnaRepository ,
        aAdjusmentRepositoryImpl $adjusmentRepository
    ) {
        $this->aBarangRepository = $aBarangRepository; 
        $this->aStokRepository = $aStokRepository;
        $this->aDeliveryOrder = $aDeliveryOrder;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->ahnaRepository = $ahnaRepository; 
        $this->adjusmentRepository = $adjusmentRepository; 
    }


    public function addAdjusmentHeader(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitCode" => "required", 
            "Notes" => "required" 
        ]);
        try {

            // Db Transaction
            DB::beginTransaction(); 

            $getmax = $this->adjusmentRepository->getMaxCode($request);
           
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
        

            $autonumber = $this->AdjusmentNumber($request, $TransactionCode);
            $this->adjusmentRepository->addAdjusmentHeader($request, $autonumber);
            
            DB::commit();
            return $this->sendResponse($autonumber, 'Transaksi Adjusment Berhasil dibuat !');

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }
    }
   
    public function addAdjusmentFinish(Request $request)
    {
       // validate 
       $request->validate([
            "TransactionCode" => "required",
            "UnitCode" => "required",   
            "Notes" => "required",
            "TotalQty" => "required",
            "TotalRow" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);

        // // cek ada gak datanya
        if ($this->adjusmentRepository->getAdjusmentbyID($request->TransactionCode)->count() < 1) {
            return $this->sendError('Mutasi Number Not Found !', []);
        }

        // validasi Kode
        foreach ($request->Items as $key) {
            # code...
            // // cek kode barangnya ada ga
            if ($this->aBarangRepository->getBarangbyId($key['ProductCode'])->count() < 1) {
                return $this->sendError('Product Not Found !', []);
            }
        }

        try {
            // Db Transaction
            DB::beginTransaction(); 
            foreach ($request->Items as $key) {
                if ($key['JenisAdjusment'] == "MINUS" ) {
                    $qty = $key['QtyStok']-$key['QtyAkhir']; 
                    $this->fifoAdjusmentMinusAdjusmentStokOnlyMigration($request,$key,$qty);
                }else{ 
                    $qty = $key['QtyAdjusment']; 
                    $this->fifoAdjusmentPlusAdjusmentStokOnlyMigration($request,$key,$qty);
                } 
                $this->adjusmentRepository->addAdjusmentFinish($request,$key);
                
                // nanti di apus kalo sudah SO
                    if ($this->ahnaRepository->getHppTrsbyKodeAdjusment($key['ProductCode'])->count() < 1) {
                        $this->ahnaRepository->addHppAdjusment($request,$key,$key['Hpp']); 
                    }
                    if ($this->ahnaRepository->getHnaTrsbyKodeAdjusment($key['ProductCode'])->count() < 1) {
                        $this->ahnaRepository->addHnaAdjusment($request, $key,$key['Hna'],$key['Hna']);  
                    }
                    $this->aBarangRepository->editHPPBarang($key,$key['Hpp']);
                    
                // nanti di apus kalo sudah SO
            }
                $this->adjusmentRepository->editAdjusmentHeader($request);
            DB::commit();
            return $this->sendResponse([], 'Transaksi Adjusment berhasil di proses !');

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaksi Gagal Di Proses !', $e->getMessage());
        }


    }
    public function getAdjusmentbyID(Request $request)
    {
           // validate 
           $request->validate([
            "TransasctionCode" => "required"
        ]);
        try {
            // cek ada gak datanya
            $data = $this->adjusmentRepository->getAdjusmentbyID($request->TransasctionCode);
            if ($data->count() < 1) {
                return $this->sendError('Transaksi Adjusment tidak di temukan !', []);
            }
           
            return $this->sendResponse($data, 'Transaksi Adjusment ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Adjusment tidak ditemukans !', $e->getMessage());
        }

    }
    public function getAdjusmentDetailbyID(Request $request)
    {
         // validate 
        $request->validate([
            "TransactionCode" => "required"
        ]);

        try {
            // cek ada gak datanya
            $data = $this->adjusmentRepository->getAdjusmentDetailbyID($request);
            if ($data->count() < 1) {
                return $this->sendError('Transaksi Adjusment tidak ditemukan !', []);
            }
            
            return $this->sendResponse($data, 'Transaksi Adjusment ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Adjusment tidak ditemukans !', $e->getMessage());
        }
    }
    public function getAdjusmentbyDateUser(Request $request)
    {
          // validate 
          $request->validate([
            "UserCreate" => "required"
        ]);
        try {
            $data = $this->adjusmentRepository->getAdjusmentbyDateUser($request);
            return $this->sendResponse($data, 'Transaksi Adjusment ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Adjusment tidak ditemukan !', $e->getMessage());
        }
    }
    public function getAdjusmentbyPeriode(Request $request)
    {
           // validate 
           $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);
        try {
            $data = $this->adjusmentRepository->getAdjusmentbyPeriode($request);
            return $this->sendResponse($data, 'Transaksi Adjusment ditemukan !');
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Transaksi Adjusment tidak ditemukan !', $e->getMessage());
        }
    }
}