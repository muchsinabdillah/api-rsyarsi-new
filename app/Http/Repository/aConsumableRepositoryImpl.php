<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aConsumableRepositoryImpl implements aConsumableRepositoryInterface
{
    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['TransactionDate']));
        return  DB::connection('sqlsrv')
        ->table('Consumables')
        ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from Consumables)"))->get();
    } 
    public function addConsumableHeader($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("Consumables")->insert([
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' => $request->UserCreate,
            'UnitCode' => $request->UnitCode, 
            'Notes' => $request->Notes, 
            'TransactionCode' => $autoNumber, 
            'Group_Transaksi' => $request->Group_Transaksi, 
            'TransactionDateFirst' => Carbon::now(),
            'UserCreateFirst' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->TransactionDate))
        ]);
    }
    public function getConsumablebyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_consumable_hdr")
        ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->get();
    }
    
    public function getConsumablebyIDTransactionandUnitID($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_consumable_hdr")
        ->where('TransactionCode', $request->TransactionCode)
        ->where('UnitCode', $request->UnitCode)
            ->where('Void', '0')
            ->get();
    }
    public function getConsumableDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_consumable_dtl")
            ->where('TransactionCode', $id)
            ->get();
    }
    public function getConsumableDetailbyIDBarang($request, $key)
    {
        return  DB::connection('sqlsrv')->table("ConsumableDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->get();
    }
 
    public function getConsumableDetailbyIDandProductCode($key)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_consumable_dtl")
        ->where('TransactionCode', $key->TransactionCode)
            ->where('ProductCode', $key->ProductCode)
            ->get();
    }
    
    public function addConsumableDetail($request,$key)
    {
        return  DB::connection('sqlsrv')->table("ConsumableDetails")->insert([
            'TransactionCode' => $request->TransactionCode,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Qty' => $key['Qty'],  
            'Konversi_QtyTotal' => $key['Konversi_QtyTotal'],  
            'Hpp' => '0',//ini emang nol atau gimana
            'Total' => '0',//ini emang nol atau gimana
            'Satuan ' =>  $key['ProductSatuan'],
            'UserVoid' => '',
            'DateAdd' => Carbon::now(),
            'UserAdd' =>  $request->UserCreate
        ]);
    }
    public function editConsumableDetailbyIdBarang($request, $key)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ConsumableDetails')
        ->where('TransactionCode', $request->TransactionCode)
        ->where('ProductCode',$key['ProductCode'])
            ->update([
                'Qty' => $key['Qty'],
                'Konversi_QtyTotal' => $key['Konversi_QtyTotal'] 
            ]);
        return $updatesatuan;
    }
    public function editConsumable($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Consumables')
        ->where('TransactionCode', $request->TransasctionCode)
            ->update([ 
            'UnitCode' => $request->UnitTujuan,
            'Group_Transaksi' => $request->Group_Transaksi, 
            'Notes' => $request->Notes,
            'TotalQtyOrder' => $request->TotalQtyOrder,
            'TotalRow' => $request->TotalRow, 
            'UserCreateLast' => $request->UserCreate,
            'TransactionDateLast' => Carbon::now() 
            ]);
        return $updatesatuan;
    }
    public function voidConsumableDetailAllOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ConsumableDetails')
            ->where('TransactionCode', $request->TransactionCode)
            ->where('Void', "0")
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidConsumable($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Consumables')
            ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidConsumablebyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ConsumableDetails')
            ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $request->ProductCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function getConsumablebyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_consumable_hdr")
            ->where('Usercreate', $request->UserCreate)
            ->get();
    }
    public function getConsumablebyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_consumable_hdr")
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->get();
    }
}
