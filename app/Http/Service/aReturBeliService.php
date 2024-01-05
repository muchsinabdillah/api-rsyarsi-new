<?php

namespace App\Http\Service;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller; 
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aDeliveryOrderRepositoryImpl;
use App\Http\Repository\aReturBeliRepositoryImpl;

class aReturBeliService extends Controller
{
    use AutoNumberTrait;
    private $aDeliveryOrder;
    private $aBarangRepository;
    private $aSupplierRepository;
    private $aPurchaseOrderRepository;
    private $aBukuStok;
    private $aStok;
    private $aHna; 
    private $aJurnal;
    private $aConsumableRepository;
    private $aMasterUnitRepository;
    private $ahnaRepository;
    private $returbeliRepository;

    public function __construct(
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $aSupplierRepository, 
        aStokRepositoryImpl $aStok,
        aDeliveryOrderRepositoryImpl $aDeliveryOrder,
        aMasterUnitRepositoryImpl $aMasterUnitRepository,
        aHnaRepositoryImpl $ahnaRepository ,
        aReturBeliRepositoryImpl $returbeliRepository
    ) {
        $this->aBarangRepository = $aBarangRepository;
        $this->aSupplierRepository = $aSupplierRepository;
        $this->aStok = $aStok;
        $this->aDeliveryOrder = $aDeliveryOrder;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
        $this->ahnaRepository = $ahnaRepository; 
        $this->returbeliRepository = $returbeliRepository; 
    }

    public function addReturBeliHeader(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitStok" => "required",
            "DeliveryCode" => "required",
            "Notes" => "required",
            "SupplierCode" => "required" 
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek deliveryCode 
            if ($this->aDeliveryOrder->getDeliveryOrderbyID($request->DeliveryCode)->count() < 1) {
                return $this->sendError('No. Delivery Order tidak ditemukan !', []);
            }


            $getmax = $this->returbeliRepository->getMaxCode($request);
           
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->ReturBeliNumber($request, $TransactionCode);
            $this->returbeliRepository->addReturBeliHeader($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Retur  Create Successfully !');

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addReturBeliFinish(Request $request)
    {
        $request->validate([
            "TransactionCode" => "required",
            "DeliveryCode" => "required",
            "UnitOrder" => "required",
            "UnitTujuan" => "required", 
            "SupplierCode" => "required",
            "Notes" => "required",
            "TotalQtyReturBeli" => "required",
            "TotalRow" => "required",
            "TotalReturBeli" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction(); 

            

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function voidReturBeli(Request $request)
    {

    }
    public function voidReturBeliDetailbyItem(Request $request)
    {

    }
    public function getReturBelibyID(Request $request)
    {

    }

    public function getReturBeliDetailbyID(Request $request)
    {

    }
    public function getReturBelibyDateUser(Request $request)
    {

    }
    public function getReturBelibyPeriode(Request $request)
    {

    }

}