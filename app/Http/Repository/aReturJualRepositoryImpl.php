<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aReturJualRepositoryImpl implements aReturJualRepositoryInterface
{
    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['TransactionDate']));
        return  DB::connection('sqlsrv')
        ->table('ReturnSales')
        ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from ReturnSales)"))->get();
    } 
    public function addReturJualHeader($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("ReturnSales")->insert([
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' => $request->UserCreate,
            'UnitCode' => $request->UnitCode,  
            'UunitSales' => $request->UnitSales,  
            'Notes' => $request->Notes, 
            'TotalQtyReturJual' => '0',
            'TotalRow' => '0', 
            'TotalQtySales' => '0', 
            'TotalReturJualRp' => '0', 
            'TransactionCode' => $autoNumber, 
            'Group_Transaksi' => $request->Group_Transaksi, 
            'SalesCode' => $request->SalesCode, 
            'NoResep' => $request->NoResep, 
            'NoRegistrasi' => $request->NoRegistrasi, 
            'TransactionDateFirst' => Carbon::now(),
            'UserCreateFirst' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->TransactionDate)),
        ]);
    }
    public function getRejualHeaderbyId($id)
    {
        return  DB::connection('sqlsrv')->table("v_retur_jual_hdr")
            ->where('TransactionCode', $id) 
            ->get();
    }
    public function getSumReturJualDetil($id)
    {
        return  DB::connection('sqlsrv')->table("v_sum_transaksi_retur_jual_detil")
            ->where('TransactionCode', $id) 
            ->get();
    }
    public function getReturJualDetailbyIDBarang($request, $key)
    {
        return  DB::connection('sqlsrv')->table("ReturnSalesDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->get();
    }
    public function addReturJualDetail($request,$key)
    {
        return  DB::connection('sqlsrv')->table("ReturnSalesDetails")->insert([
            'TransactionCode' => $request->TransactionCode,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'ProductSatuan' => $key['ProductSatuan'],
            'ReturPrice' => $key['ReturPrice'],  
            'QtySales' => $key['QtySales'],  
            'QtyReturJual' => $key['QtyReturJual'],  
            'Konversi_Qty_Total' => $key['Konversi_QtyTotal'],   
            'TotalReturJual' => $key['TotalReturJual'],  
            'SatuanBeli ' =>  $key['SatuanBeli'],
            'UserVoid' => '',
            'DateEntry' => Carbon::now(),
            'UserEntry' =>  $request->UserCreate,
            'SalesCode ' =>  $request->SalesCode
        ]);
    }
    public function editReturJualDetailbyIdBarang($request, $key)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnSalesDetails')
        ->where('TransactionCode', $request->TransactionCode)
        ->where('ProductCode',$key['ProductCode'])
            ->update([
                'QtyReturJual' => $key['QtyReturJual'],
                'Konversi_Qty_Total' => $key['Konversi_QtyTotal'],
                'TotalReturJual' => $key['TotalReturJual'],
                'DateUpdate' => Carbon::now(),
                'UserUpdate' => $request->UserCreate
            ]);
        return $updatesatuan;
    }
    public function editReturJualHeader($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnSales')
        ->where('TransactionCode', $request->TransactionCode) 
            ->update([ 
                'TotalQtyReturJual' => $request->TotalQtyReturJual,
                'TotalQtySales' => $request->TotalQtysales,
                'TotalRow' => $request->TotalRow,
                'TotalReturJualRp' => $request->TotalReturJualRp, 
                'TransactionDateLast' => Carbon::now(),
                'UserCreateLast' => $request->UserCreate
            ]);
        return $updatesatuan;
    }
    public function getReturJualbyIDUnitOrder($request)
    {
        return  DB::connection('sqlsrv')->table("v_retur_jual_hdr")
        ->where('TransactionCode',$request->TransactionCode)
            ->where('Void', '0')
            ->where('UnitCode', $request->UnitCode) 
            ->get();
    }
    public function getReturJualDetailbyIDandProductCode($request)
    {
        return  DB::connection('sqlsrv')->table("ReturnSalesDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $request->ProductCode)
            ->where('Void', '0')
            ->get();
    }
    public function getReturJualDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("ReturnSalesDetails")
        ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->get();
    }
    public function updateSumReturHeader($TransactionCode,$TotalQtyReturJual,$TotalQtySales,$TotalRow,$TotalReturJualRp,$UserCreate)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnSales')
        ->where('TransactionCode', $TransactionCode) 
            ->update([ 
                'TotalQtyReturJual' => $TotalQtyReturJual,
                'TotalQtySales' => $TotalQtySales,
                'TotalRow' => $TotalRow,
                'TotalReturJualRp' => $TotalReturJualRp, 
                'TransactionDateLast' => Carbon::now(),
                'UserCreateLast' => $UserCreate
            ]);
        return $updatesatuan;
    }
    public function getReturJualbyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_retur_jual_hdr")
        ->where('Usercreate', $request->UserCreate)
            ->where('Void', '0')
            ->get();
    }
    public function getReturJualbyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_retur_jual_hdr")
        ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Void', '0')
            ->get();
    }
    public function voidReturJualDetailbyItemAll($request,$ProductCode)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnSalesDetails')
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
     public function voidReturJual($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('ReturnSales')
        ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }

    public function getReturJualDetailbySalesCode($id)
    {
        return  DB::connection('sqlsrv')->table("ReturnSalesDetails")
        ->where('SalesCode', $id)
            ->where('Void', '0')
            ->get();
    }
}