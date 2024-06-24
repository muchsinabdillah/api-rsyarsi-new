<?php

namespace App\Http\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aFakturRepositoryImpl implements aFakturRepositoryInterface
{
    public function addFaktur($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("Fakturs")->insert([
            'TransactionDate' => $request->TransactionDate,
            'UserCreate' => $request->UserCreate,
            'SupplierCode' => $request->SupplierCode,
            'Keterangan' => $request->Keterangan,
            'DeliveryCode' => $request->DeliveryCode,
            'TipeHutang' => $request->TipeHutang,
            'TransactionCode' => $autoNumber,
            'NoFakturPBF' => $request->NoFakturPBF,
            'DateFakturPBF' => $request->DateFakturPBF,
            'NoFakturPajak' => $request->NoFakturPajak,
            'Keterangan' => $request->Keterangan,
            'DateCreateReal' => Carbon::now(),
            'UseCreateReal' => $request->UserCreate,
            'UnitPembelian' => $request->UnitPembelian,
            'ReffDateTrs' => date("dmY", strtotime($request->TransactionDate))
        ]);
    }
    public function updateFakturHeader($request,$includeppn)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Fakturs')
        ->where('TransactionCode', $request->TransactionCode) 
        ->update([
            'UserCreate' => $request->UserCreate,
            'PurchaseOrderCode' => $request->NoPurchaseOrder,
            'Pph23' => $request->Pph23,
            'SupplierCode' => $request->SupplierCode,
            'UserCreateLast' => $request->UserCreate,
            'Keterangan' => $request->Keterangan,
            'DeliveryCode' => $request->DeliveryCode,
            'TipeHutang' => $request->TipeHutang, 
            'NoFakturPBF' => $request->NoFakturPBF,
            'DateFakturPBF' => $request->DateFakturPBF,
            'NoFakturPajak' => $request->NoFakturPajak,
            'Keterangan' => $request->Keterangan,
            'TransactionDateLast' => Carbon::now(),
            'TotalRow' => $request->TotalRow,
            'TotalQty' => $request->TotalQty,
            'TotalNilaiFaktur' => $request->TotalNilaiFaktur,
            'TglJatuhTempo' => $request->TglJatuhTempo,
            'TotalDiskon' => $request->TotalDiskon,
            'TotalTax' => $request->TotalTax,
            'Subtotal' => $request->Subtotal,
            'DiskonLain' => $request->DiskonLain,
            'BiayaLain' => $request->BiayaLain,
            'Grandtotal' => $request->Grandtotal,
            'IncludePPN' => $includeppn
            ]);
        return $updatesatuan;
    }
    public function addFakturDetil($key, $kodePo,$request)
    {
        return  DB::connection('sqlsrv')->table("FakturDetails")->insert([
            'TransactionCode' => $kodePo,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'ProductSatuan' => $key['ProductSatuan'],
            'Harga' => $key['Harga'], 
            'Diskon' => $key['Diskon'],
            'Diskon2' => $key['Diskon2'],
            'Subtotal' => $key['Diskon2'],
            'Tax' => $key['Tax'],
            'Tax2' => $key['Tax2'],
            'Total' => $key['Total'],
            'QtyFaktur' => $key['QtyFaktur'],
            'QtyFakturSisa' => $key['QtyFakturSisa'],
            'DateAdd' => Carbon::now(),
            'UserAdd' =>  $request->UserCreate
        ]);
    }
    public function addHutangHeader($notes,$request, $autonumberHtg,$kdrekening)
    {
        return  DB::connection('sqlsrv4')->table("HUTANG_REKANAN")->insert([
            'KD_HUTANG' => $autonumberHtg,
            'KD_REKANAN' => $request->SupplierCode,
            'TGL_HUTANG' => $request->TransactionDate,
            'PETUGAS' => $request->UserCreate,
            'NILAI_HUTANG' => $request->Grandtotal,
            'SISA_HUTANG' => $request->Grandtotal,
            'KET' => $notes,
            'KET2' => $request->TransactionCode,
            'KET3' => $kdrekening,
            'TGL_TEMPO' => $request->TglJatuhTempo,
            'TGL_FAKTUR' => $request->DateFakturPBF,
            'NO_FAKTUR' => $request->NoFakturPBF,
            'FS_TGL_VOID' => '1900-01-01',
            'FS_KD_PETUGAS_VOID' => '',
            'KD_ORDER' => '',
        ]);
    }
    public function VoidHutangHdr($request)
    {
        return DB::connection('sqlsrv4')->table('HUTANG_REKANAN')
        ->where('KET2', $request->TransactionCode)
        ->where('FS_KD_PETUGAS_VOID', null)
            ->update([
                'FS_TGL_VOID' => $request->DateVoid,
                'FS_KD_PETUGAS_VOID' => $request->UserVoid 
            ]);
    }
    public function addHutangDetail($request, $autonumberHtg)
    {
        return  DB::connection('sqlsrv4')->table("HUTANG_REKANAN_2")->insert([
            'KD_HUTANG' => $autonumberHtg,
            'KD_JURNAL' => $request->TransactionCode
        ]);
    } 

    public function getFakturbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_faktur_hdr")
            ->where('TransactionCode', $id)
            ->get();
    }
    
    public function getFakturDetailByBarang($ProductCode, $TransactionCode)
    {
        return  DB::connection('sqlsrv')->table("FakturDetails")
            ->where('TransactionCode', $TransactionCode)
            ->where('ProductCode', $ProductCode)
            ->get();
    }
    public function editQtyDeliveryOrderRemain($request, $qtyremainDO, $productcode)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
        ->where('TransactionCode', $request->DeliveryCode)
            ->where('ProductCode', $productcode)
            ->update([
                'QtyDeliveryRemain' => $qtyremainDO
            ]);
        return $updatesatuan;
    }
    public function updateDeliveryOrdeDetails($request, $key)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('FakturDetails')
            ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $key['ProductCode'])
            ->update([
                'ProductSatuan' => $key['ProductSatuan'],
                'ProductName' => $key['ProductName'],
                'LastPrice' => $key['LastPrice'],
                'Price' => $key['Price'],
                'DiscountProsen' => $key['DiscountProsen'],
                'DiscountRp' => $key['DiscountRp'],
                'DiscountRpTTL' => $key['DiscountRpTTL'],
                'QtyPurchase' => $key['QtyPurchase'],
                'QtyDelivery' => $key['QtyDelivery'],
                'QtyDeliveryRemain' => $key['QtyDeliveryRemain'],
                'SubtotalFaktur' => $key['SubtotalFaktur'],
                'TaxProsen' => $key['TaxProsen'],
                'TaxRp' => $key['TaxRp'],
                'TaxRpTTL' => $key['TaxRpTTL'],
                'TotalFaktur' => $key['TotalFaktur'],
                'ExpiredDate' => $key['ExpiredDate'],
                'NoBatch' => $key['NoBatch'],
                'Hpp' => $key['Hpp'],
                'HppTax' => $key['HppTax'],
                'KonversiQty' => $key['KonversiQty'],
                'Konversi_QtyTotal' => $key['Konversi_QtyTotal'],
                'Satuan_Konversi' => $key['Satuan_Konversi'],
                'UserEntry' => $key['UserEntry'],
                'DateEntry' => $key['DateEntry']
            ]);
        return $updatesatuan;
    }
   
    public function voidFaktur($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('Fakturs')
            ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidFakturDetailAllOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('FakturDetails')
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
    public function voidFakturDetailbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('FakturDetails')
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
    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['TransactionDate']));
        return  DB::connection('sqlsrv')
            ->table('Fakturs')
            ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from Fakturs)"))->get();
    }
    public function getMaxCodeHutang($request)
    {
        $ddatedmy = date("dmy", strtotime($request['TransactionDate']));
        return  DB::connection('sqlsrv4')
        ->table('HUTANG_REKANAN')
        ->where(DB::raw("SUBSTRING(KD_HUTANG,3,6)"), $ddatedmy, DB::raw("(select max(`KD_HUTANG`) from HUTANG_REKANAN)"))->get();
    }
    public function getFakturDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("FakturDetails")
            ->where('TransactionCode', $id)
            ->get();
    }
    public function getFakturDetailbyIDandProductCode($request)
    {
        return  DB::connection('sqlsrv')->table("FakturDetails")
            ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $request->ProductCode)
            ->get();
    }
    public function getFakturDetailbyIDnotIdTrsNow($key, $request)
    {
        return  DB::connection('sqlsrv')->table("FakturDetails")
            ->where('TransactionCode', '<>', $request->TransactionCode)
            ->where('ProductCode', $key->ProductCode)
            ->orderBy('ID', 'DESC')
            ->get();
    }
    public function getFakturbyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_faktur_hdr")
            ->where('Usercreate', $request->UserCreate)
            ->get();
    }
    public function getFakturbyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_faktur_hdr")
            ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
            ->get();
    }
    public function delItemsbyPOnumber($key, $po)
    {

        return  DB::connection('sqlsrv')->table("PurchaseOrderDetails")
            ->where('ProductCode', $key['ProductCode'])
            ->where('PurchaseCode', $po)
            ->delete();
    }
    public function getHutangbyID($request)
    {
        return  DB::connection('sqlsrv4')->table("HUTANG_REKANAN")
        ->where('KET2', $request->TransactionCode)
            ->get();
    }
    public function updateHutangHdr($request,$kdhutang)
    {
        return DB::connection('sqlsrv4')->table('HUTANG_REKANAN')
            ->where('KD_HUTANG', $kdhutang)
            ->update([ 
                'KD_REKANAN' => $request->SupplierCode,
                'TGL_HUTANG' => $request->TransactionDate,
                'PETUGAS' => $request->UserCreate,
                'NILAI_HUTANG' => $request->Grandtotal,
                'SISA_HUTANG' => $request->Grandtotal,
                'KET' => '',
                'KET2' => $request->TransactionCode,
                'KET3' => '',
                'TGL_TEMPO' => $request->TglJatuhTempo,
                'TGL_FAKTUR' => $request->DateFakturPBF,
                'NO_FAKTUR' => $request->NoFakturPBF
            ]);
    }

    public function delHutangDtl($request, $kdhutang)
    {
        return DB::connection('sqlsrv4')->table('HUTANG_REKANAN_2')
        ->where('KD_HUTANG', $kdhutang)
            ->update([
                'KD_JURNAL' => $request->TransactionCode 
            ]); 
    }
}
