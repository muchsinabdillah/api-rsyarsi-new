<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class aStokRepositoryImpl implements aStokRepositoryInterface
{
    public function addStok($request,$key)
    { 
        return  DB::connection('sqlsrv')->table("Stoks")->insert([
            'ProductCode' => $key['ProductCode'],
            'Qty' => $key['QtyDelivery']* $key['KonversiQty'],
            'Satuan' => $key['Satuan_Konversi'],
            'Layanan' => $request->UnitCode 
        ]);
    }
    public function addStokTrs($request, $key, $qtynew,$Unit)
    {
        return  DB::connection('sqlsrv')->table("Stoks")->insert([
            'ProductCode' => $key['ProductCode'],
            'Qty' => $qtynew,
            'Satuan' => $key['Satuan_Konversi'],
            'Layanan' => $Unit
        ]);
    }
    public function addBukuStok($request, $key,$nilaiHppFix)
    {
        $qtystok = $key['QtyDelivery'] * $key['KonversiQty'];
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' =>  $request->UserCreate,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Satuan' => $key['Satuan_Konversi'],
            'QtyIn' => $qtystok ,
            'QtySisa' => $qtystok ,
            'Hpp' => $nilaiHppFix,
            'PersediaanIn' => $nilaiHppFix*$qtystok ,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => 'DO',
            'Status' => '1',
            'ExpiredDate' => $key['ExpiredDate'],
            'DeliveryCode' => $request->TransactionCode,
            'Unit' => $request->UnitCode,
            'BatchNumber' => $key['NoBatch']
        ]);
    }
    public function addDataStoks($request, $key,$nilaiHppFix)
    {
        $qtystok = $key['QtyDelivery'] * $key['KonversiQty'];
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' =>  $request->UserCreate,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Satuan' => $key['Satuan_Konversi'],
            'QtyIn' => $qtystok ,
            'QtySisa' => $qtystok ,
            'Hpp' => $nilaiHppFix,
            'PersediaanIn' => $nilaiHppFix*$qtystok ,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => 'DO',
            'Status' => '1',
            'ExpiredDate' => $key['ExpiredDate'],
            'DeliveryCode' => $request->TransactionCode,
            'Unit' => $request->UnitCode,
            'BatchNumber' => $key['NoBatch']
        ]);
    }
    public function addBukuStokVoid($request, $key,$reff_void)
    {
        $qtystok = $key->QtyDelivery* $key->KonversiQty;
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan_Konversi,
            'QtyOut' => $qtystok,
            'Hpp' => $key->Hpp,
            'PersediaanOut' => $key->Hpp * $qtystok,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => $reff_void,
            'Status' => '2',
            'DeliveryCode' => $request->TransactionCode,
            'Unit' => $request->UnitCode,
            'ExpiredDate' => $request->ExpiredDate,
            'BatchNumber' => $key->NoBatch
        ]);
    }
    public function addDataStoksVoid($request, $key,$reff_void)
    {
        $qtystok = $key->QtyDelivery* $key->KonversiQty;
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan_Konversi,
            'QtyOut' => $qtystok,
            'Hpp' => $key->Hpp,
            'PersediaanOut' => $key->Hpp * $qtystok,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => $reff_void,
            'Status' => '2',
            'DeliveryCode' => $request->TransactionCode,
            'Unit' => $request->UnitCode,
            'ExpiredDate' => $request->ExpiredDate,
            'BatchNumber' => $key->NoBatch
        ]);
    }
    public function addBukuStokVoidbyIdProduct($request, $dtlDo)
    {
        $qtystok = $dtlDo->QtyDelivery * $dtlDo->KonversiQty;
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $dtlDo->ProductCode,
            'ProductName' => $dtlDo->ProductName,
            'Satuan' => $dtlDo->Satuan_Konversi,
            'QtyOut' => $qtystok,
            'Hpp' => $dtlDo->Hpp,
            'PersediaanOut' => $dtlDo->Hpp * $qtystok,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => 'DO_V',
            'Status' => '2',
            'DeliveryCode' => $request->TransactionCode,
            'Unit' => $request->UnitCode,
            'ExpiredDate' => $request->ExpiredDate,
            'BatchNumber' => $dtlDo->NoBatch
        ]);
    }
    public function addDataStokVoidbyIdProduct($request, $dtlDo)
    {
        $qtystok = $dtlDo->QtyDelivery * $dtlDo->KonversiQty;
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $dtlDo->ProductCode,
            'ProductName' => $dtlDo->ProductName,
            'Satuan' => $dtlDo->Satuan_Konversi,
            'QtyOut' => $qtystok,
            'Hpp' => $dtlDo->Hpp,
            'PersediaanOut' => $dtlDo->Hpp * $qtystok,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => 'DO_V',
            'Status' => '2',
            'DeliveryCode' => $request->TransactionCode,
            'Unit' => $request->UnitCode,
            'ExpiredDate' => $request->ExpiredDate,
            'BatchNumber' => $dtlDo->NoBatch
        ]);
    }
    public function deleteBukuStok($request, $key, $tipetrs,$unit)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")
        ->where('ProductCode', $key['ProductCode'])
        ->where('TransactionCodeReff', $request->TransactionCode)
        ->where('TransactionCodeReff2', $tipetrs)
        ->where('Unit', $unit)
        ->delete();
    }
    public function deleteDataStoks($request, $key, $tipetrs,$unit)
    {
        return  DB::connection('sqlsrv')->table("DataStoks")
        ->where('ProductCode', $key['ProductCode'])
        ->where('TransactionCodeReff', $request->TransactionCode)
        ->where('TransactionCodeReff2', $tipetrs)
        ->where('Unit', $unit)
        ->delete();
    }
    public function updateStok($request, $key, $QtyTotal)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Stoks')
        ->where('ProductCode', $key['ProductCode'])
        ->where('Layanan', $request->UnitCode)
            ->update([
                'Qty' => $QtyTotal,
                'Satuan' =>  $key['Satuan_Konversi']
            ]);
        return $updatesatuan;
    }
    
    public function updateStokTrs($request, $key, $QtyTotal, $Unit)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Stoks')
        ->where('ProductCode', $key['ProductCode'])
        ->where('Layanan', $Unit)
            ->update([
                'Qty' => $QtyTotal,
            'Satuan' =>  $key['Satuan_Konversi']
            ]);
        return $updatesatuan;
    }
    public function updateStokPerItemMutasi($productcode, $QtyTotal, $Unit)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Stoks')
        ->where('ProductCode', $productcode)
        ->where('Layanan', $Unit)
            ->update([
                'Qty' => $QtyTotal
            ]);
        return $updatesatuan;
    }
    public function cekStokbyIDBarang($key, $unit)
    {
        return  DB::connection('sqlsrv')->table("v_stok")
        ->where('ProductCode', $key['ProductCode'])
        ->where('Layanan', $unit)
        ->get();
    }
    public function getStokBarangbyUnitNameLike($request)
    {
        return  DB::connection('sqlsrv')
            ->table("v_stok") 
            ->where('Layanan',$request->unit)
            ->where('NamaBarang', 'like', '%' . $request->name . '%')->get();
    }
    public function getStokBarangbyUnit($request)
    {
        return  DB::connection('sqlsrv')
            ->table("v_stok") 
            ->where('Layanan',$request->unit)->get();
    }
    public function getBukuStokBarangbyUnit($request)
    {
        return  DB::connection('sqlsrv')
            ->table("v_buku_stok") 
            ->where('Unit',$request->unit)
            ->where('ProductCode',$request->ProductCode) 
            ->whereBetween('TransactionDate', [$request->PeriodeAwal, $request->PeriodeAkhir])
            ->get();
    }
    public function getBukuStokBarangBeforebyUnit($request)
    {

        return  DB::connection('sqlsrv')->table("BukuStoks")
        ->where('Unit',$request->unit)
        ->where('ProductCode',$request->ProductCode) 
        ->whereBetween('TransactionDate', [$request->PeriodeAwal, $request->PeriodeAkhir])
        -> select(
            'BukuStoks.ProductCode',
            'BukuStoks.ProductName',
            'BukuStoks.Unit' ,
            DB::raw('SUM(QtyIn) - SUM(QtyOut) AS stok')
            ) 
            ->groupBy('BukuStoks.ProductCode','BukuStoks.ProductName','BukuStoks.Unit') 
            ->get();
 
    }
    public function cekStokbyIDBarangOnly($id, $request)
    {
        return  DB::connection('sqlsrv')->table("v_stok")
        ->where('ProductCode', $id)
        ->where('Layanan', $request->UnitCode)
            ->get();
    }
    public function cekStokbyIDBarangOnlyMutasi($id, $unit)
    {
        return  DB::connection('sqlsrv')->table("v_stok")
        ->where('ProductCode', $id)
        ->where('Layanan', $unit)
            ->get();
    }
    public function updateStokPerItemBarang($request, $key, $QtyTotal)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Stoks')
        ->where('ProductCode', $key)
        ->where('Layanan', $request->UnitCode)
            ->update([
                'Qty' => $QtyTotal
            ]);
        return $updatesatuan;
    }
    public function getStokExpiredFirst($request, $key)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")
        ->where('ProductCode', $key['ProductCode'])
        ->where('Unit', $request->UnitTujuan)
        // ->where('Status', '1')
        -> select(
            'BukuStoks.DeliveryCode',
            'BukuStoks.Hpp',
            'BukuStoks.BatchNumber',
            DB::raw('SUM(QtyIn)-SUM(QtyOut)  AS x'),
            DB::raw("replace(CONVERT(VARCHAR(11),ExpiredDate, 111), '/','-') as ExpiredDate")
            )
            ->groupBy('BukuStoks.ExpiredDate','BukuStoks.DeliveryCode','BukuStoks.Hpp', 'BukuStoks.BatchNumber')
            ->having(DB::raw('SUM(QtyIn)-SUM(QtyOut)'), '>', 0)
            ->orderBy('ExpiredDate','asc')
            ->get()->first();
    }
    public function getStokExpiredFirstGlobal($request, $key,$unit)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")
        ->where('ProductCode', $key['ProductCode'])
        // ->where('Status', '1')
        ->where('Unit', $unit)
        -> select(
            'BukuStoks.DeliveryCode',
            'BukuStoks.Hpp',
            'BukuStoks.BatchNumber',
            DB::raw('SUM(QtyIn)-SUM(QtyOut)  AS x'),
            DB::raw("replace(CONVERT(VARCHAR(11),ExpiredDate, 111), '/','-') as ExpiredDate")
            )
            ->groupBy('BukuStoks.ExpiredDate','BukuStoks.DeliveryCode','BukuStoks.Hpp', 'BukuStoks.BatchNumber')
            ->having(DB::raw('SUM(QtyIn)-SUM(QtyOut)'), '>', 0)
            ->orderBy('ExpiredDate','asc')
            ->get()->first();
    }
    public function addBukuStokIn($request, $key, $TipeTrs, $DeliveryCode, $xhpp, $ExpiredDate,$BatchNumber, $qtynew, $persediaan, $UnitIn)
    {
        if($TipeTrs == "MT"){
            $qtystok = $qtynew; 
        }else{
            $qtystok ="0";
        }
        if($TipeTrs == "AD"){
            $qtystok = $qtynew; 
        }
        if($TipeTrs == "TRJ"){
            $qtystok = $qtynew; 
        }
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' =>  $request->UserCreate,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Satuan' => $key['Satuan_Konversi'],
            'QtyIn' => $qtystok,
            'Hpp' => $xhpp,
            'PersediaanIn' => $persediaan,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => $TipeTrs,
            'Status' => '1',
            'ExpiredDate' => $ExpiredDate,
            'DeliveryCode' => $DeliveryCode,
            'Unit' => $UnitIn,
            'BatchNumber' => $BatchNumber
        ]);
    }
    public function addDataBukuStokIn($request, $key, $TipeTrs, $DeliveryCode, $xhpp, $ExpiredDate,$BatchNumber, $qtynew, $persediaan, $UnitIn)
    {
        if($TipeTrs == "MT"){
            $qtystok = $qtynew; 
        }else{
            $qtystok ="0";
        }
        if($TipeTrs == "AD"){
            $qtystok = $qtynew; 
        }
        if($TipeTrs == "TRJ"){
            $qtystok = $qtynew; 
        }
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' =>  $request->UserCreate,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Satuan' => $key['Satuan_Konversi'],
            'QtyIn' => $qtystok,
            'Hpp' => $xhpp,
            'PersediaanIn' => $persediaan,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => $TipeTrs,
            'Status' => '1',
            'ExpiredDate' => $ExpiredDate,
            'DeliveryCode' => $DeliveryCode,
            'Unit' => $UnitIn,
            'BatchNumber' => $BatchNumber
        ]);
    }
    public function addBukuStokOut($request, $key, $TipeTrs, $DeliveryCode, $xhpp, $ExpiredDate,$BatchNumber, $qtynew, $persediaan, $UnitOut)
    {
        if ($TipeTrs == "MT") {
            $qtystok = $qtynew; 
        } else {
            $qtystok = "0";
        }
        if ($TipeTrs == "CM") {
            $qtystok = $qtynew; 
        } 
        if ($TipeTrs == "RB") {
            $qtystok = $qtynew; 
        } 
        if ($TipeTrs == "TPR") {
            $qtystok = $qtynew; 
        } 
        if($TipeTrs == "AD"){
            $qtystok = $qtynew; 
        }
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' =>  $request->UserCreate,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Satuan' => $key['Satuan_Konversi'],
            'QtyOut' => $qtystok,
            'Hpp' => $xhpp,
            'PersediaanOut' => $persediaan,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => $TipeTrs,
            'Status' => '1',
            'ExpiredDate' => $ExpiredDate,
            'DeliveryCode' => $DeliveryCode,
            'Unit' => $UnitOut,
            'BatchNumber' => $BatchNumber
        ]);
    }
    public function addDataStokOut($request, $key, $TipeTrs, $DeliveryCode, $xhpp, $ExpiredDate,$BatchNumber, $qtynew, $persediaan, $UnitOut)
    {
        if ($TipeTrs == "MT") {
            $qtystok = $qtynew; 
        } else {
            $qtystok = "0";
        }
        if ($TipeTrs == "CM") {
            $qtystok = $qtynew; 
        } 
        if ($TipeTrs == "RB") {
            $qtystok = $qtynew; 
        } 
        if ($TipeTrs == "TPR") {
            $qtystok = $qtynew; 
        } 
        if($TipeTrs == "AD"){
            $qtystok = $qtynew; 
        }
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $request->TransactionCode,
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' =>  $request->UserCreate,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Satuan' => $key['Satuan_Konversi'],
            'QtyOut' => $qtystok,
            'Hpp' => $xhpp,
            'PersediaanOut' => $persediaan,
            'TransactionCodeReff' => $request->TransactionCode,
            'TransactionCodeReff2' => $TipeTrs,
            'Status' => '1',
            'ExpiredDate' => $ExpiredDate,
            'DeliveryCode' => $DeliveryCode,
            'Unit' => $UnitOut,
            'BatchNumber' => $BatchNumber
        ]);
    }
    public function addBukuStokInVoidFromSelect($key,$reff,$request)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $key->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan,
            'QtyIn' => $key->QtyOut,
            'Hpp' => $key->Hpp,
            'PersediaanIn' => $key->PersediaanOut,
            'TransactionCodeReff' =>$key->TransactionCodeReff,
            'TransactionCodeReff2' => $reff,
            'Status' => '2',
            'ExpiredDate' => $key->ExpiredDate,
            'DeliveryCode' => $key->DeliveryCode,
            'Unit' =>$key->Unit,
            'BatchNumber' => $key->BatchNumber
        ]); 
    }
    public function addDataStoksInVoidFromSelect($key,$reff,$request)
    {
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $key->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan,
            'QtyIn' => $key->QtyOut,
            'Hpp' => $key->Hpp,
            'PersediaanIn' => $key->PersediaanOut,
            'TransactionCodeReff' =>$key->TransactionCodeReff,
            'TransactionCodeReff2' => $reff,
            'Status' => '2',
            'ExpiredDate' => $key->ExpiredDate,
            'DeliveryCode' => $key->DeliveryCode,
            'Unit' =>$key->Unit,
            'BatchNumber' => $key->BatchNumber
        ]); 
    }
    public function addBukuStokOutVoidFromSelect($key,$reff,$request)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $key->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan,
            'QtyIn' => $key->QtyOut,
            'Hpp' => $key->Hpp,
            'PersediaanIn' => $key->PersediaanOut,
            'TransactionCodeReff' =>$key->TransactionCodeReff,
            'TransactionCodeReff2' => $reff,
            'Status' => '2',
            'ExpiredDate' => $key->ExpiredDate,
            'DeliveryCode' => $key->DeliveryCode,
            'Unit' =>$key->Unit,
            'BatchNumber' => $key->BatchNumber
        ]);
    }
    public function addBukuStokInVoidFromSelectMutasi($key,$reff,$request,$Konversi_QtyTotal,$unit)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $key->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan,
            'QtyIn' => $Konversi_QtyTotal,
            'Hpp' => $key->Hpp,
            'PersediaanIn' => $Konversi_QtyTotal*$key->Hpp,
            'TransactionCodeReff' =>$key->TransactionCodeReff,
            'TransactionCodeReff2' => $reff,
            'Status' => '2',
            'ExpiredDate' => $key->ExpiredDate,
            'DeliveryCode' => $key->DeliveryCode,
            'Unit' =>$unit,
            'BatchNumber' => $key->BatchNumber
        ]); 
    }
    public function addDataStoksInVoidFromSelectMutasi($key,$reff,$request,$Konversi_QtyTotal,$unit)
    {
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $key->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan,
            'QtyIn' => $Konversi_QtyTotal,
            'Hpp' => $key->Hpp,
            'PersediaanIn' => $Konversi_QtyTotal*$key->Hpp,
            'TransactionCodeReff' =>$key->TransactionCodeReff,
            'TransactionCodeReff2' => $reff,
            'Status' => '2',
            'ExpiredDate' => $key->ExpiredDate,
            'DeliveryCode' => $key->DeliveryCode,
            'Unit' =>$unit,
            'BatchNumber' => $key->BatchNumber
        ]); 
    }
    public function addBukuStokOutVoidFromSelectMutasi($key,$reff,$request,$Konversi_QtyTotal,$unit)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")->insert([
            'TransactionCode' => $key->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan,
            'QtyOut' => $Konversi_QtyTotal,
            'Hpp' => $key->Hpp,
            'PersediaanOut' => $Konversi_QtyTotal*$key->Hpp,
            'TransactionCodeReff' =>$key->TransactionCodeReff,
            'TransactionCodeReff2' => $reff,
            'Status' => '2',
            'ExpiredDate' => $key->ExpiredDate,
            'DeliveryCode' => $key->DeliveryCode,
            'Unit' =>$unit,
            'BatchNumber' => $key->BatchNumber
        ]);
    }
    public function addDataStoksOutVoidFromSelectMutasi($key,$reff,$request,$Konversi_QtyTotal,$unit)
    {
        return  DB::connection('sqlsrv')->table("DataStoks")->insert([
            'TransactionCode' => $key->TransactionCode,
            'TransactionDate' => Carbon::now(),
            'UserCreate' =>  $request->UserVoid,
            'ProductCode' => $key->ProductCode,
            'ProductName' => $key->ProductName,
            'Satuan' => $key->Satuan,
            'QtyOut' => $Konversi_QtyTotal,
            'Hpp' => $key->Hpp,
            'PersediaanOut' => $Konversi_QtyTotal*$key->Hpp,
            'TransactionCodeReff' =>$key->TransactionCodeReff,
            'TransactionCodeReff2' => $reff,
            'Status' => '2',
            'ExpiredDate' => $key->ExpiredDate,
            'DeliveryCode' => $key->DeliveryCode,
            'Unit' =>$unit,
            'BatchNumber' => $key->BatchNumber
        ]);
    }
    public function cekBukuByTransactionandCodeProduct($id, $request,$typetrsReff)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")
        ->where('ProductCode', $id)
        ->where('TransactionCodeReff2', $typetrsReff)
        ->where('TransactionCode', $request->TransactionCode)
        ->get();
    }
    public function getQtyPersediaan($request, $key)
    {
        return  DB::connection('sqlsrv')->table("BukuStoks")
        ->where('ProductCode', $key['ProductCode'])
        ->where('Unit', $request->UnitTujuan)
        -> select( 
            DB::raw('(isnull(sum(isnull(QtyIn,0))-sum(isnull(QtyOut,0)),0))  AS SaldoQty'),
            'hpp','ProductCode','DeliveryCode',
            DB::raw('CAST((isnull(sum(isnull(QtyIn,0))-sum(isnull(QtyOut,0)),0))*hpp  AS DECIMAL(18,0) )  as persediaan') )
            ->groupBy('BukuStoks.Hpp','BukuStoks.ProductCode','BukuStoks.DeliveryCode')
            ->having(DB::raw('(isnull(sum(isnull(QtyIn,0))-sum(isnull(QtyOut,0)),0))'), '>', 0)
            ->orderBy('DeliveryCode','asc')
            ->get();
    }
    
}