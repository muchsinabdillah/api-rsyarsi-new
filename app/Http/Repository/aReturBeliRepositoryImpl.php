<?php

namespace App\Http\Repository;

use Carbon\Carbon;
use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use App\Http\Repository\aReturBeliRepositoryInterface;

class aReturBeliRepositoryImpl implements aReturBeliRepositoryInterface
{
    public function addReturBeliHeader($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("ReturnPurchases")->insert([
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' => $request->UserCreate,
            'UnitCode' => $request->UnitStok, 
            'Notes' => $request->Notes,
            'SupplierCode' => $request->SupplierCode, 
            'TransactionCode' => $autoNumber,
            'TransactionDateFirst' => Carbon::now(),
            'UserCreateFirst' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->TransactionDate))
        ]);
    }
    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['TransactionDate']));
        return  DB::connection('sqlsrv')
        ->table('ReturnPurchases')
        ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from ReturnPurchases)"))->get();
    }
    public function addReturBeliDetail($request)
    {
        return  DB::connection('sqlsrv')->table("ReturnPurchaseDetails")->insert([
            'TransactionCode' => $request->TransactionCode,
            'ProductCode' => $request->ProductCode,
            'ProductName' => $request->ProductName,
            'Price' => $request->Price,
            'QtyPurchase' => $request->QtyPurchase,
            'QtyRetur' => $request->QtyRetur,
            'Satuan' =>  $request->Satuan, 
            'TotalReturBeli' =>  $request->TotalReturBeli, 
            'DateAdd' => Carbon::now(),
            'UserAdd' =>  $request->UserAdd
        ]);
    }
}