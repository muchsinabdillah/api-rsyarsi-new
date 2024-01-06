<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aOrderMutasiRepositoryImpl implements aOrderMutasiRepositoryInterface
{
    public function addOrderMutasi($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("OrderMutasis")->insert([
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' => $request->UserCreate,
            'UnitOrder' => $request->UnitOrder,
            'UnitTujuan' => $request->UnitTujuan,
            'Notes' => $request->Notes,
            'JenisMutasi' => $request->JenisMutasi,
            'JenisStok' => $request->JenisStok,
            'TransactionCode' => $autoNumber,
            'TransactionDateFirst' => Carbon::now(),
            'UserCreateFirst' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->TransactionDate))
        ]);
    }
    public function addOrderMutasiDetail($request)
    {
        return  DB::connection('sqlsrv')->table("OrderMutasiDetails")->insert([
            'TransactionCode' => $request->TransactionCode,
            'ProductCode' => $request->ProductCode,
            'ProductName' => $request->ProductName,
            'QtyStok' => $request->QtyStok,
            'QtyOrderMutasi' => $request->QtyOrderMutasi,
            'QtySisaMutasi' => $request->QtySisaMutasi,
            'Satuan' =>  $request->Satuan,
            'Satuan_Konversi' =>  $request->Satuan_Konversi,
            'KonversiQty' =>  $request->KonversiQty,
            'Konversi_QtyTotal' =>  $request->Konversi_QtyTotal,
            'DateAdd' => Carbon::now(),
            'UserAdd' =>  $request->UserAdd
        ]);
    }
    public function editOrderMutasi($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderMutasis')
            ->where('TransactionCode', $request->TransasctionCode)
            ->update([ 
                'UnitOrder' => $request->UnitOrder,
                'UnitTujuan' => $request->UnitTujuan,
                'TotalQtyOrder' => $request->TotalQtyOrder,
                'TotalRow' => $request->TotalRow,
                'Notes' => $request->Notes,
                'UserCreate' => $request->UserCreate,
                'JenisMutasi' => $request->JenisMutasi,
                'JenisStok' => $request->JenisStok,
                'TransactionDate' => $request->TransactionDate
            ]);
        return $updatesatuan;
    }

    public function getOrderMutasibyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_order_mutasi_hdr")
            ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->get();
    }
    public function getOrderMutasiApprovedbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_order_mutasi_hdr")
        ->where('TransactionCode', $id)
            ->where('UserApproved', '<>', '')
            ->get();
    }
    public function getItemsDouble($request)
    {
        return  DB::connection('sqlsrv')->table("OrderMutasiDetails")
            ->where('TransactionCode', $request->TransactionCode)
            ->where('Void', '0')
            ->where('ProductCode', $request->ProductCode)
            ->get();
    }
    public function voidOrderMutasi($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderMutasis')
            ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidOrderMutasiDetailbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderMutasiDetails')
            ->where('TransactionCode', $request->TransasctionCode)
            ->where('ProductCode', $request->ProductCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidOrderMutasiDetailAll($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderMutasiDetails')
        ->where('TransactionCode', $request->TransactionCode) 
        ->where('Void', '0') 
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['TransactionDate']));
        return  DB::connection('sqlsrv')
            ->table('OrderMutasis')
            ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from OrderMutasis)"))->get();
    }
    public function getOrderMutasiDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("OrderMutasiDetails")
            ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->where('QtySisaMutasi','<>', '0')
            ->get();
    }
    public function getOrderMutasiDetailbyIDBarang($request, $key)
    {
        return  DB::connection('sqlsrv')->table("OrderMutasiDetails")
            ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->get();
    }
    public function getOrderMutasiDetailbyIDBarangMutasi($TransactionCode, $ProductCode)
    {
        return  DB::connection('sqlsrv')->table("OrderMutasiDetails")
            ->where('TransactionCode', $TransactionCode)
            ->where('ProductCode', $ProductCode)
            ->where('Void', '0')
            ->get();
    }
    public function updateQtyOrderMutasi($request, $key, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderMutasiDetails')
            ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->update([
                'QtySisaMutasi' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function updateQtyOrderMutasi2($request, $key, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderMutasiDetails')
        ->where('TransactionCode', $request->TransactionOrderCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->update([
                'QtySisaMutasi' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function updateQtyOrderMutasi3($request, $Productcode, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderMutasiDetails')
        ->where('TransactionCode', $request->TransactionOrderCode)
            ->where('ProductCode', $Productcode)
            ->where('Void', '0')
            ->update([
                'QtySisaMutasi' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function getOrderMutasibyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_order_mutasi_hdr")
            ->where('Usercreate', $request->UserCreate)
            ->where('Void', '0')
            ->get();
    }
    public function getOrderMutasibyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_order_mutasi_hdr")
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Void', '0')
            ->get();
    }
}
