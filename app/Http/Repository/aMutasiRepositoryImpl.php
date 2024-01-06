<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aMutasiRepositoryImpl implements aMutasiRepositoryInterface
{
    public function addMutasi($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("Mutasis")->insert([
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' => $request->UserCreate,
            'UnitOrder' => $request->UnitOrder,
            'UnitTujuan' => $request->UnitTujuan,
            'Notes' => $request->Notes,
            'JenisMutasi' => $request->JenisMutasi,
            'JenisStok' => $request->JenisStok,
            'TransactionCode' => $autoNumber,
            'TransactionOrderCode' => $request->TransactionOrderCode,
            'TransactionDateFirst' => Carbon::now(),
            'UserCreateFirst' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->TransactionDate))
        ]);
    }
    public function addMutasiDetail($request,$key,$xhpp)
    {
        return  DB::connection('sqlsrv')->table("MutasiNewDetails")->insert([
            'TransactionCode' => $request->TransactionCode,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'QtyMutasi' => $key['QtyMutasi'],
            'QtyOrder' => $key['QtyOrderMutasi'],
            'QtySisa' => $key['QtyOrderMutasi'],
            'Satuan_Konversi' => $key['Satuan_Konversi'],
            'KonversiQty' => $key['KonversiQty'],
            'Konversi_QtyTotal' => $key['Konversi_QtyTotal'],
            'ExpiredDate' => '',
            'Hpp' => $xhpp,
            'Total' => $xhpp*$key['Konversi_QtyTotal'],
            'Satuan ' =>  $key['ProductSatuan'],
            'DateAdd' => Carbon::now(),
            'UserAdd' =>  $request->UserAdd
        ]);
    }
    public function editMutasiDetailbyIdBarang($request, $key,$hpp)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('MutasiNewDetails')
        ->where('TransactionCode', $request->TransactionCode)
        ->where('ProductCode',$key['ProductCode'])
            ->update([
                'KonversiQty' => $key['KonversiQty'],
                'Konversi_QtyTotal' => $key['Konversi_QtyTotal'],
                'QtyMutasi' => $key['QtyMutasi'],
                'Hpp' => $hpp,
                'Total' => $hpp*$key['Konversi_QtyTotal']
            ]);
        return $updatesatuan;
    }
    public function editMutasi($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Mutasis')
        ->where('TransactionCode', $request->TransasctionCode)
            ->update([
            'UnitOrder' => $request->UnitOrder,
            'UnitTujuan' => $request->UnitTujuan,
            'JenisMutasi' => $request->JenisMutasi,
            'JenisStok' => $request->JenisStok,
            'Notes' => $request->Notes,
            'TotalQtyOrder' => $request->TotalQtyOrder,
            'TotalRow' => $request->TotalRow,
            'UserCreate' => $request->UserCreate,
            'UserCreateLast' => $request->UserCreate,
            'TransactionDateLast' => Carbon::now()
            ]);
        return $updatesatuan;
    }
    
    public function getMutasibyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_mutasi_hdr")
        ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->get();
    }
    
    public function getMutasibyIDUnitOrder($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_mutasi_hdr")
        ->where('TransactionCode',$request->TransactionCode)
            ->where('Void', '0')
            ->where('UnitOrder', $request->UnitOrder)
            ->where('UnitTujuan', $request->UnitTujuan)
            ->get();
    }
    

    public function getItemsDouble($request)
    {
        return  DB::connection('sqlsrv')->table("MutasiNewDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('Void', '0')
            ->where('ProductCode', $request->ProductCode)
            ->get();
    }
    public function voidMutasi($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Mutasis')
        ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidMutasiDetailbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('MutasiNewDetails')
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $request->ProductCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidMutasiDetailbyItemAll($request,$ProductCode)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('MutasiNewDetails')
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
    public function voidMutasiDetailAll($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('MutasiNewDetails')
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
        ->table('Mutasis')
        ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from Mutasis)"))->get();
    }
    public function getMutasiDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("MutasiNewDetails")
        ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->get();
    }
    public function getMutasiDetailbyIDBarang($request, $key)
    {
        return  DB::connection('sqlsrv')->table("MutasiNewDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->get();
    }
    public function updateQtyMutasi($request, $key, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('MutasiNewDetails')
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->update([
                'QtySisaMutasi' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function getMutasibyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_mutasi_hdr")
        ->where('Usercreate', $request->UserCreate)
            ->where('Void', '0')
            ->get();
    }
    public function getMutasibyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_mutasi_hdr")
        ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Void', '0')
            ->get();
    }
    public function getMutasiDetailbyIDandProductCode($request)
    {
        return  DB::connection('sqlsrv')->table("MutasiNewDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $request->ProductCode)
            ->where('Void', '0')
            ->get();
    }
}