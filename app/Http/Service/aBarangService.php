<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use Exception;
use Illuminate\Support\Facades\Log;

class aBarangService extends Controller
{

    private $aBarangRepository;
    private $aSupplierRepository;

    public function __construct(
        aBarangRepositoryImpl $aBarangRepository,
        aSupplierRepositoryImpl $aSupplierRepository
        )
    {
        $this->aBarangRepository = $aBarangRepository;
        $this->aSupplierRepository = $aSupplierRepository;
    }

    public function addBarang(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "ProductCode" => "required",
            "ProductName" => "required",
            "ProductNameAlias" => "required",
            "Discontinue" => "required",
            "Category" => "required",
            "Group_DK" => "required",
            "Satuan_Beli" => "required",
            "Unit_Satuan" => "required",
            "Konversi_satuan" => "required",
            "Reorder_Level" => "required",
            "Signa" => "required",
            "Description" => "required", 
            "Kode_Barcode" => "required",
            "flag_telemedicine" => "required",
            "KD_PDP" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
            $createBarang = $this->aBarangRepository->addBarang($request);
            if ($createBarang) {
                //response
                return response()->json([
                    "status" => 1,
                    "message" => "Product Add Successfully"
                ], 200);
            } else {
                //response
                return response()->json([
                    "status" => 0,
                    "message" => "Product Add Failed"
                ], 500);
            } 
    } 
    public function editBarang(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "ProductCode" => "required",
            "ProductName" => "required",
            "ProductNameAlias" => "required",
            "Discontinue" => "required",
            "Category" => "required",
            "Group_DK" => "required",
            "Satuan_Beli" => "required",
            "Unit_Satuan" => "required",
            "Konversi_satuan" => "required",
            "Reorder_Level" => "required",
            "Signa" => "required",
            "Description" => "required",
            "Kode_Barcode" => "required",
            "flag_telemedicine" => "required",
            "KD_PDP" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // create new user 
        $getSataun = $this->aBarangRepository->getBarangbyId($request->ID)->count();
        if ($getSataun == 0) {
            //response
            return response()->json([
                "status" => 0,
                "message" => "Product Not Found"
            ], 500);
        } else {
            $createBarang = $this->aBarangRepository->editBarang($request);
            if ($createBarang) {
                //response
                return response()->json([
                    "status" => 1,
                    "message" => "Product Edit Successfully"
                ], 200);
            } else {
                //response
                return response()->json([
                    "status" => 0,
                    "message" => "Product Edit Failed"
                ], 500);
            }
        }
    }
    public function getBarangbyId($id)
    {
        // validator 
        $count = $this->aBarangRepository->getBarangbyId($id)->count();

        if ($count > 0) {
            $data = $this->aBarangRepository->getBarangbyId($id);
            return $this->sendResponse($data, "Data Product ditemukan.");
        } else {
            return $this->sendError("Data Product Found.", [], 400);
        }
    }
    public function getBarangAll()
    {
        // validator 
        $count = $this->aBarangRepository->getBarangAll()->count();
        if ($count > 0) {
            $data = $this->aBarangRepository->getBarangAll();
            return $this->sendResponse($data, "Data Barang ditemukan.");
        } else {
            return $this->sendError("Data Product Not Found.", [], 400);
        }
    }
    public function addBarangSupplier(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "IDBarang" => "required",
            "IDSupplier" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // cek if product supplier exist
        if ($this->aBarangRepository->getBarangbyIdAndIDSupplier($request)->count() > 0) {
            return response()->json([
                "status" => 0,
                "message" => "Product ID :" . $request->IDBarang . " with Id Supplier : " 
                                . $request->IDSupplier . " Already Exist."
            ], 500);
        }
        if ($this->aSupplierRepository->getSupplierbyId($request->IDSupplier)->count() < 1) {
            return response()->json([
                "status" => 0,
                "message" => " Id Supplier : " . $request->IDSupplier . " Invalid."
            ], 500);
        }
        
        // add
        $createBarang = $this->aBarangRepository->addBarangSupplier($request);
        if ($createBarang) {
            //response
            return response()->json([
                "status" => 1,
                "message" => "Product Supplier Add Successfully"
            ], 200);
        } else {
            //response
            return response()->json([
                "status" => 0,
                "message" => "Product Supplier Add Failed"
            ], 500);
        }
    }
    public function deleteBarangSupplier(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "IDBarang" => "required",
            "IDSupplier" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        // cek if product supplier exist
        if ($this->aBarangRepository->getBarangbyIdAndIDSupplier($request)->count() < 1) {
            return response()->json([
                "status" => 0,
                "message" => "Product ID :" . $request->IDBarang . " with Id Supplier : " . $request->IDSupplier . " Not Found."
            ], 500);
        } 
        // delete
        $createBarang = $this->aBarangRepository->deleteBarangSupplier($request);
        if ($createBarang) {
            //response
            return response()->json([
                "status" => 1,
                "message" => "Product Supplier Deleted Successfully"
            ], 200);
        } else {
            //response
            return response()->json([
                "status" => 0,
                "message" => "Product Supplier  Deleted Failed"
            ], 500);
        }
    }
    public function getBarangbySuppliers($id)
    {
        // validator 
        $count = $this->aBarangRepository->getBarangbySuppliers($id)->count();

        if ($count > 0) {
            $data = $this->aBarangRepository->getBarangbySuppliers($id);
            return $this->sendResponse($data, "Data Product ditemukan.");
        } else {
            return $this->sendError("Data Product Supplier Found.", [], 400);
        }
    }


    //barang Formularium
    public function addBarangFormularium(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "IDBarang" => "required",
            "IDFormularium" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // cek if product supplier exist
        if ($this->aBarangRepository->getBarangbyIdAndIDFormularium($request)->count() > 0) {
            return response()->json([
                "status" => 0,
                "message" => "Product ID :" . $request->IDBarang . " with Id Formularium : " . $request->IDFormularium . " Already Exist."
            ], 500);
        }
        // delete
        $createBarang = $this->aBarangRepository->addBarangFormularium($request);
        if ($createBarang) {
            //response
            return response()->json([
                "status" => 1,
                "message" => "Product Formularium Add Successfully"
            ], 200);
        } else {
            //response
            return response()->json([
                "status" => 0,
                "message" => "Product Formularium Add Failed"
            ], 500);
        }
    }
    public function deleteBarangFormularium(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "IDBarang" => "required",
            "IDFormularium" => "required"
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // cek if product supplier exist
        if ($this->aBarangRepository->getBarangbyIdAndIDFormularium($request)->count() < 1) {
            return response()->json([
                "status" => 0,
                "message" => "Product ID :" . $request->IDBarang . " with Id Formularium : " . $request->IDFormularium . " Not Found."
            ], 500);
        }
        // delete
        $createBarang = $this->aBarangRepository->deleteBarangFormularium($request);
        if ($createBarang) {
            //response
            return response()->json([
                "status" => 1,
                "message" => "Product Deleted Successfully"
            ], 200);
        } else {
            //response
            return response()->json([
                "status" => 0,
                "message" => "Product Deleted Failed"
            ], 500);
        }
    }
    public function getBarangbyFormulariums($id)
    {
        // validator 
        $count = $this->aBarangRepository->getBarangbyFormulariums($id)->count();

        if ($count > 0) {
            $data = $this->aBarangRepository->getBarangbyFormulariums($id);
            return $this->sendResponse($data, "Data Product ditemukan.");
        } else {
            return $this->sendError("Data Product Found.", [], 400);
        }
    }
    public function getBarangbyNameLike(Request $request){
        $count = $this->aBarangRepository->getBarangbyNameLike($request)->count();
        
        if($count > 0){
            $data = $this->aBarangRepository->getBarangbyNameLike($request);
            return $this->sendResponse($data, "Data Product ditemukan.");
        }else{
            return $this->sendError("Data Product Found.", [], 400);
        }

       
    }

    public function getPrinterLabelAll()
    {
        // validator 
        $count = $this->aBarangRepository->getPrinterLabelAll()->count();
        if ($count > 0) {
            $data = $this->aBarangRepository->getPrinterLabelAll();
            return $this->sendResponse($data, "Data Barang ditemukan.");
        } else {
            return $this->sendError("Data Product Not Found.", [], 400);
        }
    }

    public function getPrinterLabelbyId($id)
    {
        // validator 
        $count = $this->aBarangRepository->getPrinterLabelbyId($id)->count();
        
        if ($count > 0) {
            $data = $this->aBarangRepository->getPrinterLabelbyId($id);
            return $this->sendResponse($data, "Data Product ditemukan.");
        } else {
            return $this->sendError("Data Product Found.", [], 400);
        }
    }

    public function addPrinterLabel(Request $request)
    {
        $request->validate([
            "IP_Komputer" => "required",
            "Jenis" => "required",
            "IPPrinterSharing" => "required",
            "NamaPrinterSharing" => "required",
        ]);
        
        try {
            // // Db Transaction
            // $data = $this->aBarangRepository->getPrinterLabelbyId($request->ID);
            // // // cek ada gak datanya
            // if ($data->count() < 1) {
            //     return $this->sendError('Data Not Found !', []);
            // }
            $createBarang = $this->aBarangRepository->addPrinterLabel($request);
            return $this->sendResponse($createBarang, 'Sharing Printer Label Berhasil Dibuat !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Not Found !', $e->getMessage());
        }
    } 

    public function editPrinterLabel(Request $request)
    {
        $request->validate([
            "ID" => "required",
            "IP_Komputer" => "required",
            "Jenis" => "required",
            "IPPrinterSharing" => "required",
            "NamaPrinterSharing" => "required",
        ]);
        
        try {
            // Db Transaction
            $data = $this->aBarangRepository->getPrinterLabelbyId($request->ID);
            // // cek ada gak datanya
            if ($data->count() < 1) {
                return $this->sendError('Data Not Found !', []);
            }
            $updateBarang = $this->aBarangRepository->editPrinterLabel($request);
            return $this->sendResponse($updateBarang, 'Sharing Printer Label Berhasil Diperbarui !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Not Found !', $e->getMessage());
        }
    } 

    public function getIPUnitFarmasiAll()
    {
        // validator 
        $count = $this->aBarangRepository->getIPUnitFarmasiAll()->count();
        if ($count > 0) {
            $data = $this->aBarangRepository->getIPUnitFarmasiAll();
            return $this->sendResponse($data, "Data Barang ditemukan.");
        } else {
            return $this->sendError("Data Product Not Found.", [], 400);
        }
    }

    public function getIPUnitFarmasibyId($id)
    {
        // validator 
        $count = $this->aBarangRepository->getIPUnitFarmasibyId($id)->count();

        if ($count > 0) {
            $data = $this->aBarangRepository->getIPUnitFarmasibyId($id);
            return $this->sendResponse($data, "Data Product ditemukan.");
        } else {
            return $this->sendError("Data Product Found.", [], 400);
        }
    }

    public function addIPUnitFarmasi(Request $request)
    {
        $request->validate([
            "IPAddress" => "required",
            "UnitCode" => "required",
        ]);
        
        try {
            $cek = $this->aBarangRepository->getIPUnitFarmasibyIP($request->IPAddress)->count();
            if ($cek > 0){
                return $this->sendError('IP ini sudah pernah digunakan ! Edit IP ini untuk mengubahnya !', []);
            }
            $createBarang = $this->aBarangRepository->addIPUnitFarmasi($request);
            return $this->sendResponse($createBarang, 'Data Berhasil Dibuat !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Not Found !', $e->getMessage());
        }
    } 

    public function editIPUnitFarmasi(Request $request)
    {
        $request->validate([
            "ID" => "required",
            "IPAddress" => "required",
            "UnitCode" => "required",
        ]);
        
        try {
            // Db Transaction
            $data = $this->aBarangRepository->getIPUnitFarmasibyId($request->ID);
            // // cek ada gak datanya
            if ($data->count() < 1) {
                return $this->sendError('Data Not Found !', []);
            }
            $updateBarang = $this->aBarangRepository->editIPUnitFarmasi($request);
            return $this->sendResponse($updateBarang, 'Data Berhasil Diperbarui !');
        } catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Not Found !', $e->getMessage());
        }
    } 

    public function getIPUnitFarmasibyIP($ip)
    {
        // validator 
        $count = $this->aBarangRepository->getIPUnitFarmasibyIP($ip)->count();

        if ($count > 0) {
            $data = $this->aBarangRepository->getIPUnitFarmasibyIP($ip);
            return $this->sendResponse($data, "Data ditemukan.");
        } else {
            return $this->sendError("Data Not Found.", [], 400);
        }
    }
}
