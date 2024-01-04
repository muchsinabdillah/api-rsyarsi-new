<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aOrderMutasiRepositoryImpl; 
use App\Http\Repository\aPurchaseRequisitionRepositoryImpl;
use App\Http\Repository\aStokRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use Exception;
use App\Traits\AutoNumberTrait;

use Illuminate\Support\Str;

class aOrderMutasiService extends Controller
{
    use AutoNumberTrait;
    private $aBarangRepository;
    private $asupplierRepository;
    private $aPurchaseRequestRepository;
    private $aStokRepository;
    private $aOrderMutasiRepository;
    private $aMasterUnitRepository;

    public function __construct(
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $asupplierRepository,
        aPurchaseRequisitionRepositoryImpl $aPurchaseRequestRepository,
        aStokRepositoryImpl $aStokRepository,
        aOrderMutasiRepositoryImpl $aOrderMutasiRepository,
        aMasterUnitRepositoryImpl $aMasterUnitRepository

    ) {
        $this->aBarangRepository = $aBarangRepository;
        $this->asupplierRepository = $asupplierRepository;
        $this->aPurchaseRequestRepository = $aPurchaseRequestRepository;
        $this->aStokRepository = $aStokRepository;
        $this->aOrderMutasiRepository = $aOrderMutasiRepository;
        $this->aMasterUnitRepository = $aMasterUnitRepository;
    }
    public function addOrderMutasi(Request $request)
    {
        // validate 
        $request->validate([
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitTujuan" => "required",
            "UnitOrder" => "required",
            "Notes" => "required",
            "JenisMutasi" => "required",
            "JenisStok" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek kode 
            if ($this->aMasterUnitRepository->getUnitById($request->UnitOrder)->count() < 1) {
                return $this->sendError('Unit Order Code Not Found !', []);
            }
            if ($this->aMasterUnitRepository->getUnitById($request->UnitTujuan)->count() < 1) {
                return $this->sendError('Unit Tujuan Code Not Found !', []);
            }

            $getmax = $this->aOrderMutasiRepository->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->OrderMutasiNumber($request, $TransactionCode);

            $this->aOrderMutasiRepository->addOrderMutasi($request, $autonumber);
            DB::commit();
            return $this->sendResponse($autonumber, 'Order Mutasi Create Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function addOrderMutasiDetail(Request $request)
    {
        try {
            // Db Transaction
            DB::beginTransaction(); 
            $request->validate([
                "TransactionCode" => "required",
                "ProductCode" => "required",
                "ProductName" => "required",
                "Satuan" => "required",
                "Satuan_Konversi" => "required",
                "KonversiQty" => "required",
                "Konversi_QtyTotal" => "required",
                "QtyStok" => "required",
                "QtyOrderMutasi" => "required",
                "QtySisaMutasi" => "required",
                "UserAdd" => "required" 
            ]);

                // cek ada gak datanya
               
                if ($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransactionCode)->count() < 1) {
                    return $this->sendError('Order Mutasi Not Found !', []);
                }
                // cek kode barangnya ada ga
                if ($this->aBarangRepository->getBarangbyId($request->ProductCode)->count() < 1) {
                    return $this->sendError('Product Not Found !', []);
                }
                // // //cek barang dobel gak 
                if ($this->aOrderMutasiRepository->getItemsDouble($request)->count() > 0) {
                    return $this->sendError('Product Code Double !', []);
                }
                // cek sudah di approved belum 
                if ($this->aOrderMutasiRepository->getOrderMutasiApprovedbyID($request->TransactionCode)->count() > 0) {
                    return $this->sendError('Order Mutasi Has Been Approved !', []);
                }
                $this->aOrderMutasiRepository->addOrderMutasiDetail($request);
            DB::commit();
            return $this->sendResponse([], 'Order Mutasi Item Add Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Data Transaction Gagal ditambahkan !', $e->getMessage());
        }
    }
    public function editOrderMutasi(Request $request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required",
            "TransactionDate" => "required",
            "UserCreate" => "required",
            "UnitOrder" => "required",
            "UnitTujuan" => "required",
            "TotalQtyOrder" => "required",
            "TotalRow" => "required",
            "JenisMutasi" => "required",
            "JenisStok" => "required", 
            "Notes" => "required"
        ]);
        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Order Mutasi Not Found !', []);
            } 
            // cek sudah di approved belum 
            if ($this->aOrderMutasiRepository->getOrderMutasiApprovedbyID($request->TransasctionCode)->count() > 0) {
                return $this->sendError('Order Mutasi Has Been Approved !', []);
            }
            if ($request->TotalRow < 1) {
                return $this->sendError('There is No Items, Edited Cancelled !', []);
            }
            if ($request->TotalQtyOrder < 1) {
                return $this->sendError('There is No Qty Items, Edited Cancelled !', []);
            }
            $this->aOrderMutasiRepository->editOrderMutasi($request);

            DB::commit();
            return $this->sendResponse([], 'Order Mutasi Edited Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Edited Failed !', $e->getMessage());
        }
    }
    public function voidOrderMutasi(Request $request)
    {
        // validate  
        $request->validate([
            "TransactionCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required",
            "ReasonVoid" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            // cek ada gak datanya
            if ($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransactionCode)->count() < 1) {
                return $this->sendError('Order Mutasi Not Found !', []);
            }
            // cek sudah di approved belum 
            if ($this->aOrderMutasiRepository->getOrderMutasiApprovedbyID($request->TransactionCode)->count() > 0) {
                return $this->sendError('Order Mutasi Has Been Approved !', []);
            }
            $this->aOrderMutasiRepository->voidOrderMutasiDetailAll($request);
            $this->aOrderMutasiRepository->voidOrderMutasi($request);

            DB::commit();
            return $this->sendResponse([], 'Order Mutasi Void Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Transaction Void Failed !', $e->getMessage());
        }
    }
    public function voidOrderMutasiDetailbyItem(Request $request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required",
            "ProductCode" => "required",
            "DateVoid" => "required",
            "UserVoid" => "required",
            "Void" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Order Mutasi Not Found !', []);
            }
            // cek sudah di approved belum 
            if ($this->aOrderMutasiRepository->getOrderMutasiApprovedbyID($request->TransasctionCode)->count() > 0) {
                return $this->sendError('Order Mutasi Has Been Approved !', []);
            }

            $this->aOrderMutasiRepository->voidOrderMutasiDetailbyItem($request);

            DB::commit();
            return $this->sendResponse([], 'Order Mutasi Void Successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Order Mutasi Void Failed !', $e->getMessage());
        }
    }
    public function getOrderMutasibyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aOrderMutasiRepository->getOrderMutasibyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aOrderMutasiRepository->getOrderMutasibyID($request->TransasctionCode);

            DB::commit();
            return $this->sendResponse($data, 'Order Mutasi Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Order Mutasi Data Not Found !', $e->getMessage());
        }
    }
    public function getOrderMutasiDetailbyID($request)
    {
        // validate 
        $request->validate([
            "TransasctionCode" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            // cek ada gak datanya
            if ($this->aOrderMutasiRepository->getOrderMutasiDetailbyID($request->TransasctionCode)->count() < 1) {
                return $this->sendError('Transaction Number Not Found !', []);
            }

            $data = $this->aOrderMutasiRepository->getOrderMutasiDetailbyID($request->TransasctionCode);

            DB::commit();
            return $this->sendResponse($data, 'Order Mutasi Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Order Mutasi Data Not Found !', $e->getMessage());
        }
    }
    public function getOrderMutasibyDateUser($request)
    {
        // validate 
        $request->validate([
            "UserCreate" => "required"
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aOrderMutasiRepository->getOrderMutasibyDateUser($request);

            DB::commit();
            return $this->sendResponse($data, 'Order Mutasi Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Order Mutasi Data Not Found !', $e->getMessage());
        }
    }
    public function getOrderMutasibyPeriode($request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);

        try {
            // Db Transaction
            DB::beginTransaction();

            $data = $this->aOrderMutasiRepository->getOrderMutasibyPeriode($request);

            DB::commit();
            return $this->sendResponse($data, 'Order Mutasi Data Found !');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->sendError('Order Mutasi Data Not Found !', $e->getMessage());
        }
    }
}
