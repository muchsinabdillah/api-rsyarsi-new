<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\UserRepositoryImpl;
use App\Http\Controllers\Controller;
use App\Http\Repository\aSupplierRepositoryImpl;

class aSupplierService extends Controller
{

    private $aSupplierRepository;

    public function __construct(aSupplierRepositoryImpl $aSupplierRepository)
    {
        $this->aSupplierRepository = $aSupplierRepository;
    }

    public function addSupplier(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "IdPabrikan" => "required",
            "Company" => "required",
            "last_name" => "required",
            "first_name" => "required",
            "Email_Address" => "required",
            "home_phone" => "required",
            "mobile_phone" => "required", 
            "Address" => "required",
            "lock" => "required",
            "suplier" => "required" ,
            "LeadTime" => "required" ,
            "JatuhTempo" => "required" ,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        // create new user 
        // $getSataun = $this->aSupplierRepository->getSupplierbyId($request)->count();
        // if ($getSataun > 0) {
        //     //response
        //     return response()->json([
        //         "status" => 0,
        //         "message" => "Supplier Already Exist"
        //     ], 500);
        // } else {
            $createSupplier = $this->aSupplierRepository->addSupplier($request);
            if ($createSupplier) {
                //response
                return response()->json([
                    "status" => 1,
                    "message" => "Supplier Add Successfully"
                ], 200);
            } else {
                //response
                return response()->json([
                    "status" => 0,
                    "message" => "Supplier Add Failed"
                ], 500);
            }
        // }
    }

    public function editSupplier(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "ID" => "required",
            "IdPabrikan" => "required",
            "Company" => "required",
            "last_name" => "required",
            "first_name" => "required",
            "Email_Address" => "required",
            "home_phone" => "required",
            "mobile_phone" => "required",
            "Address" => "required",
            "lock" => "required",
            "suplier" => "required" 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        // create new user 
        $getSataun = $this->aSupplierRepository->getSupplierbyId($request->ID)->count();
        if ($getSataun == 0) {
            //response
            return response()->json([
                "status" => 0,
                "message" => "Supplier Not Found"
            ], 500);
        } else {
            $createSupplier = $this->aSupplierRepository->editSupplier($request);
            if ($createSupplier) {
                //response
                return response()->json([
                    "status" => 1,
                    "message" => "Supplier Edit Successfully"
                ], 200);
            } else {
                //response
                return response()->json([
                    "status" => 0,
                    "message" => "Supplier Edit Failed"
                ], 500);
            }
        }
    }
    public function getSupplierbyId($id)
    {
        // validator 
        $count = $this->aSupplierRepository->getSupplierbyId($id)->count();

        if ($count > 0) {
            $data = $this->aSupplierRepository->getSupplierbyId($id);
            return $this->sendResponse($data, "Data User ditemukan.");
        } else {
            return $this->sendError("Data User Not Found.", []);
        }
    }
    public function getSupplierAll()
    {
        // validator 
        $count = $this->aSupplierRepository->getSupplierAll()->count();

        if ($count > 0) {
            $data = $this->aSupplierRepository->getSupplierAll();
            return $this->sendResponse($data, "Data Supplier ditemukan.");
        } else {
            return $this->sendError("Data User Not Found.", []);
        }
    }
}
