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
            'UnitCode' => $request->UnitCode, 
            'Notes' => $request->Notes,
            'SupplierCode' => $request->SupplierCode, 
            'TransactionCode' => $autoNumber,
            'TransactionDateFirst' => Carbon::now(),
            'UserCreateFirst' => $request->UserCreate,
            'DeliveryCode' => $request->DeliveryCode,
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
    public function addReturBeliDetail($request,$TransactionCode)
    {
        return  DB::connection('sqlsrv')->table("ReturnPurchaseDetails")->insert([
            'TransactionCode' => $TransactionCode,
            'ProductCode' => $request['ProductCode'],
            'ProductName' => $request['ProductName'],
            'ProductSatuan' => $request['ProductSatuan'],
            'ReturPrice' => $request['HargaRetur'],
            'DeliveryPrice' => $request['HargaBeli'],
            'QtyPurchase' => $request['QtyDeliveryRemain'],
            'QtyRetur' => $request['QtyRetur'],
            'Konversi_Qty_Total' =>  $request['QtyRetur']*$request['KonversiQty'], 
            'Satuan' =>  $request['Satuan'], 
            'TotalReturBeli' =>  $request['TotalHargaRetur'], 
            'DateEntry' => Carbon::now() ,
            'UserEntry' =>  $request['UserAdd']
        ]);
    }
    public function gettempReturDoStok($request){
        return  DB::connection('sqlsrv')->table("V_ReturWithStokDo") 
            ->where('Layanan', $request->UnitCode) 
            ->where('TransactionCode', $request->DeliveryCode) 
            ->get();
    }
    public function getReturBeliDetailbyIDBarang($request, $key)
    {
        return  DB::connection('sqlsrv')->table("ReturnPurchaseDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->get();
    }
    public function editReturBeliDetaibyIdBarang($request, $key)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnPurchaseDetails')
        ->where('TransactionCode', $request->TransactionCode)
        ->where('ProductCode',$key['ProductCode'])
            ->update([
                'QtyRetur' => $key['QtyRetur'],
                'TotalReturBeli' => $key['TotalHargaRetur'],
                 'UserUpdate' => $key['UserAdd'], 
                 'DateUpdate' =>  Carbon::now() 
            ]);
        return $updatesatuan;
    }
    public function getReturBelibyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_returberli_hdr")
        ->where('TransactionCode', $id) 
        ->get();
    }
    public function getReturBelibyIDUnitOrder($request)
    {
        return  DB::connection('sqlsrv')->table("v_returberli_hdr")
        ->where('TransactionCode',$request->TransactionCode) 
            ->where('UnitCode', $request->UnitCode) 
            ->get();
    }
    public function getReturBeliDetailbyIDandProductCode($request)
    {
        return  DB::connection('sqlsrv')->table("ReturnPurchaseDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $request->ProductCode)
            ->where('Void', '0')
            ->get();
    }
    public function voidReturBeliDetailbyItem($request,$ProductCode)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnPurchaseDetails')
            ->where('TransactionCode', $request->TransactionCode)
                ->where('ProductCode', $ProductCode)
                ->update([
                    'Void' => $request->Void,
                    'DateVoid' => $request->DateVoid,
                    'UserVoid' => $request->UserVoid,
                    'ReasonVoid' => $request->ReasonVoid
                ]);
            return $updatesatuan;
    }
    public function voidReturBeli($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnPurchases')
            ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function getReturBeliDetailbyIDOnly($request)
    {
        return  DB::connection('sqlsrv')->table("ReturnPurchaseDetails")
        ->where('TransactionCode', $request->TransactionCode) 
            ->where('Void', '0')
            ->get();
    }
    public function getReturBelibyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_returberli_hdr")
        ->where('Usercreate', $request->UserCreate)
            ->where('Void', '0')
            ->get();
    }
    public function getReturBelibyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_returberli_hdr")
        ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Void', '0')
            ->get();
    }
}