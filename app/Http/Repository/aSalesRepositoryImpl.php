<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aSalesRepositoryImpl implements aSalesRepositoryInterface
{
    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['TransactionDate']));
        return  DB::connection('sqlsrv')
        ->table('Sales')
        ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from Sales)"))->get();
    } 
    public function addSalesHeader($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("Sales")->insert([
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' => $request->UserCreate,
            'UnitOrder' => $request->UnitOrder, 
            'UnitSales' => $request->UnitTujuan, 
            'Notes' => $request->Notes, 
            'TotalQtyOrder' => '0',
            'TotalRow' => '0', 
            'TotalSales' => '0', 
            'TransactionCode' => $autoNumber, 
            'Group_Transaksi' => $request->Group_Transaksi, 
            'NoResep' => $request->NoResep, 
            'NoRegistrasi' => $request->NoRegistrasi, 
            'TransactionDateFirst' => Carbon::now(),
            'UserCreateFirst' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->TransactionDate)),
            'NamaPembeli' => $request->NamaPembeli,
            'GenderPembeli' => $request->GenderPembeli,
            'AlamatPembeli' => $request->AlamatPembeli,
            'TglLahirPembeli' => $request->TglLahirPembeli,
            'JenisPasien' => $request->JenisPasien,
            'NamaJaminan' => $request->Jaminan,
            'NIPKaryawan' => $request->NIP_Karyawan,
            'GroupJaminan' => $request->GroupJaminan,
            'KodeJaminan' => $request->KodeJaminan
        ]);
    }
    public function getSalesbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
        ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->get();
    }
    public function getSalesDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_dtl")
            ->where('TransactionCode', $id)
            ->get();
    }
    public function getSalesDetailbyIDBarang($request, $key)
    {
        return  DB::connection('sqlsrv')->table("SalesDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->get();
    }
    public function getSalesDetailbyIDBarangv2($request, $ProductCode)
    {
        return  DB::connection('sqlsrv')->table("SalesDetails")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $ProductCode)
            ->where('Void', '0')
            ->get();
    }
    public function getSalesDetailbyIDBarangFix($idTrs, $kodeBarang)
    {
        return  DB::connection('sqlsrv')->table("SalesDetails")
        ->where('TransactionCode', $idTrs)
            ->where('ProductCode', $kodeBarang)
            ->where('Void', '0')
            ->get();
    }
    public function getSalesDetailbyIDandProductCode($key)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_dtl")
        ->where('TransactionCode', $key->TransactionCode)
            ->where('ProductCode', $key->ProductCode)
            ->get();
    }
    public function getSalesbyIDTransactionandUnitID($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
        ->where('TransactionCode', $request->TransactionCode)
        ->where('UnitCode', $request->UnitCode)
            ->where('Void', '0')
            ->get();
    }
    public function addSalesDetail($request,$key,$xhpp)
    {
        return  DB::connection('sqlsrv')->table("SalesDetails")->insert([
            'TransactionCode' => $request->TransactionCode,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'Qty' => $key['Qty'],  
            'QtyResep' => $key['QtyResep'],  
            'Satuan' => $key['ProductSatuan'],  
            'Harga' => $key['Harga'],  
            'Discount' => $key['Discount'],  
            'Subtotal' => $key['Subtotal'],  
            'Tax' => $key['Tax'],  
            'Grandtotal' => $key['Grandtotal'],  
            'UangR' => $key['UangR'],  
            'Embalase' => $key['Embalase'],  
            'Hpp' =>  $xhpp,   
            'UserVoid' => '',
            'DateAdd' => Carbon::now(),
            'UserAdd' =>  $request->UserCreate,
            'Konversi_QtyTotal' => $key['Konversi_QtyTotal'],//tambahan
            'AturanPakai' => $key['AturanPakai'],  //tambahan
            'QtySalesRemain' => $key['QtyResep']  //tambahan
        ]);
    }
    public function addSalesDetailV2($request,$xhpp)
    {
        return  DB::connection('sqlsrv')->table("SalesDetails")->insert([
            'TransactionCode' => $request->TransactionCode,
            'ProductCode' => $request->ProductCode,
            'ProductName' => $request->ProductName,
            'Qty' => $request->Qty,  
            'QtyResep' => $request->QtyResep,  
            'Satuan' => $request->ProductSatuan,  
            'Harga' => $request->Harga,  
            'Discount' => $request->Discount,  
            'Subtotal' => $request->Subtotal,  
            'Tax' => $request->Tax,  
            'Grandtotal' => $request->Grandtotal,  
            'UangR' => '0',  
            'Embalase' => '0',  
            'Hpp' =>  $xhpp,   
            'UserVoid' => '',
            'DateAdd' => Carbon::now(),
            'UserAdd' =>  $request->UserCreate,
            'Konversi_QtyTotal' => $request->Konversi_QtyTotal,
            'AturanPakai' => $request->AturanPakai,
            'QtySalesRemain' => $request->QtyResep
        ]);
    }
    public function editSalesDetailbyIdBarang($request, $key,$xhpp)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('SalesDetails')
        ->where('TransactionCode', $request->TransactionCode)
        ->where('ProductCode',$key['ProductCode'])
            ->update([
                'Qty' => $key['Qty'],  
                'QtyResep' => $key['QtyResep'],  
                'Satuan' => $key['ProductSatuan'],  
                'Harga' => $key['Harga'],  
                'Discount' => $key['Discount'],  
                'Hpp' =>  $xhpp,   
                'Subtotal' => $key['Subtotal'],  
                'Tax' => $key['Tax'],  
                'Grandtotal' => $key['Grandtotal'], 
            ]);
        return $updatesatuan;
    }
    public function editSales($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Sales')
        ->where('TransactionCode', $request->TransactionCode)
            ->update([ 
                'UnitOrder' => $request->UnitOrder, 
                'UnitSales' => $request->UnitTujuan, 
                'Notes' => $request->Notes, 
                'TotalQtyOrder' => $request->TotalQtyOrder,
                'TotalRow' => $request->TotalRow, 
                'TotalSales' => $request->TotalSales, 
                'Discount' => $request->Discount, 
                'Subtotal' => $request->Subtotal, 
                'Tax' => $request->Tax, 
                'Grandtotal' => $request->Grandtotal, 
                'UserCreateLast' => $request->UserCreateLast,
                'TransactionDateLast' => Carbon::now() ,
                //'HasilReview' => $request->HasilReview,
            ]);
        return $updatesatuan;
    }
    public function voidSalesDetailAllOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('SalesDetails')
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
    public function voidSales($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Sales')
            ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidSalesbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('SalesDetails')
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
    public function getSalesbyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->where('Usercreate', $request->UserCreate)
            ->get();
    }
    public function getSalesbyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->get();
    }
    public function getSalesbyNoResep($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->where('NoResep', $request->NoResep)
            ->get();
    }
    public function updateQtRemainSalesDetail($TransactionCode, $ProductCode, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('SalesDetails')
        ->where('TransactionCode', $TransactionCode)
            ->where('ProductCode', $ProductCode)
            ->where('Void', '0')
            ->update([
                'QtySalesRemain' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function getConsumableChargedPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->where('Group_Transaksi', 'CONSUMABLE')
            ->whereBetween('TglPeriode', [$request->tglPeriodeAwal, $request->tglPeriodeAkhir])
            ->get();
    }
    public function getSalesDetailbyNoReg($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_dtl")
        ->join('v_transaksi_sales_hdr','v_transaksi_sales_hdr.TransactionCode','=','v_transaksi_sales_dtl.TransactionCode')
        ->select('v_transaksi_sales_dtl.*')
            ->where('NoRegistrasi', $request->NoRegistrasi)
             //->where('Group_Transaksi', 'RESEP')
             ->where('UnitOrder', $request->UnitSales)
             ->where('UnitSales', $request->UnitCode)
            ->get();
    }

    public function getSalesHeaderbyNoReg($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->where('NoRegistrasi', $id)
            ->get();
    }

    public function getSalesDetailbyIDDetail($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_dtl")
            ->where('ID', $id)
            ->get();
    }

    public function getSalesbyPeriodeResep($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Group_Transaksi','RESEP')
            ->get();
    }

    public function getSalesbyIDandNoResep($id,$noresep)
    {
        //13112024
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")    
        ->join('v_transaksi_orderresep_hdr','v_transaksi_sales_hdr.NoResep','=','v_transaksi_orderresep_hdr.OrderID')
        ->where('v_transaksi_sales_hdr.TransactionCode', $id)
        ->where('v_transaksi_sales_hdr.NoResep', $noresep)
            ->where('v_transaksi_sales_hdr.Void', '0')
            ->select('v_transaksi_sales_hdr.*','v_transaksi_orderresep_hdr.NamaDokter','v_transaksi_orderresep_hdr.TglResepRaw','v_transaksi_orderresep_hdr.KodeKelas','Iter','v_transaksi_orderresep_hdr.HasilReview')
            ->get();
    }

    public function getSalesDetailbyIDandNoResep($id,$noresep)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_dtl")
        ->join('v_transaksi_sales_hdr','v_transaksi_sales_hdr.TransactionCode','=','v_transaksi_sales_dtl.TransactionCode')
        ->leftJoin('v_transaksi_orderresep_dtl','v_transaksi_sales_hdr.NoResep','=','v_transaksi_orderresep_dtl.IdOrderResep')
        ->whereColumn('v_transaksi_sales_dtl.ProductCode','v_transaksi_orderresep_dtl.KodeBarang')
        ->where('v_transaksi_sales_hdr.TransactionCode', $id)
        ->where('v_transaksi_sales_hdr.NoResep', $noresep)
            ->where('v_transaksi_sales_hdr.Void', '0')
            ->where('v_transaksi_sales_dtl.Void', '0')
            ->where('v_transaksi_orderresep_dtl.Batal', '0')
            ->where('v_transaksi_orderresep_dtl.QryRealisasi','<>', '0')
            ->get();
    }
    public function editAturanPakaibyIdBarang($request, $key)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('SalesDetails')
        ->where('TransactionCode', $request->TransactionCode)
        ->where('ProductCode',$key['ProductCode'])
        ->where('Void','0')
            ->update([
                'AturanPakai' => $key['AturanPakai'], 
            ]);
        return $updatesatuan;
    }

    public function getSalesbyPeriodeTanpaResep($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Group_Transaksi','NON RESEP')
            ->get();
    }

    public function getSalesDetailbyNoRegIDBarang($request,$ProductCode)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_dtl")
        ->join('v_transaksi_sales_hdr','v_transaksi_sales_hdr.TransactionCode','=','v_transaksi_sales_dtl.TransactionCode')
        ->select('v_transaksi_sales_dtl.*')
            ->where('NoRegistrasi', $request->NoRegistrasi)
             ->where('Group_Transaksi', 'RESEP')
             ->where('UnitOrder', $request->UnitSales)
             ->where('UnitSales', $request->UnitCode)
             ->where('ProductCode', $ProductCode)
            ->get();
    }

    public function updateQtRemainSalesDetailbyNoReg($NoRegistrasi, $ProductCode, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('SalesDetails')
        ->join('Sales','Sales.TransactionCode','=','SalesDetail.TransactionCode')
            ->where('SalesDetail.ProductCode', $ProductCode)
            ->where('SalesDetail.Void', '0')
            ->where('Sales.Void', '0')
            ->where('Sales.NoRegistrasi', $NoRegistrasi)
            ->update([
                'QtySalesRemain' => $qtyRemain
            ]);
        return $updatesatuan;
    }

    //tambahan 30-10-2024 code:05112024
    //13112024
    public function getSalesbyPeriodeResepRajal($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
             ->leftjoin('AntrianObatFarmasi','AntrianObatFarmasi.NoResep','=','v_transaksi_sales_hdr.NoResep')
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Group_Transaksi','RESEP')
            ->where(DB::raw("LEFT(v_transaksi_sales_hdr.NoRegistrasi,2)"),'RJ')
            ->select('v_transaksi_sales_hdr.*','AntrianObatFarmasi.StatusAntrean')
            ->get();
    }
    
    public function getSalesbyPeriodeResepRanap($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_sales_hdr")
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->where('Group_Transaksi','RESEP')
            ->where(DB::raw("LEFT(NoRegistrasi,2)"),'RI')
            ->get();
    }

}