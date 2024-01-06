<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aDeliveryOrderRepositoryImpl implements aDeliveryOrderRepositoryInterface
{
    public function addDeliveryOrder($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("DeliveryOrders")->insert([
            'DeliveryOrderDate' => $request->DeliveryOrderDate,
            'UserCreate' => $request->UserCreate,
            'SupplierCode' => $request->SupplierCode,
            'Notes' => $request->Notes,
            'PurchaseOrderCode' => $request->PurchaseOrderCode,
            'JenisDelivery' => $request->JenisDelivery,
            'TransactionCode' => $autoNumber,
            'DateCreateReal' => Carbon::now(),
            'UseCreateReal' => $request->UserCreate,
            'UnitCode' => $request->UnitCode,
            'ReffDateTrs' => date("dmY", strtotime($request->DeliveryOrderDate))
        ]);
    }
    public function addDeliveryOrderDetil($key, $kodePo,$nilaiHppFix)
    {
        return  DB::connection('sqlsrv')->table("DeliveryOrderDetails")->insert([
            'TransactionCode' => $kodePo,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'ProductSatuan' => $key['ProductSatuan'],
            'LastPrice' => $key['LastPrice'],
            'Price' => $key['Price'],
            'DiscountProsen' => $key['DiscountProsen'],
            'DiscountRp' => $key['DiscountRp'],
            'DiscountRpTTL' => $key['DiscountRpTTL'],
            'QtyPurchase' => $key['QtyPurchase'],
            'QtyDelivery' => $key['QtyDelivery'],
            'QtyDeliveryRemain' => $key['QtyDelivery'],
            'SubtotalDeliveryOrder' => $key['SubtotalDeliveryOrder'],
            'TaxProsen' => $key['TaxProsen'],
            'TaxRp' => $key['TaxRp'],
            'TaxRpTTL' => $key['TaxRpTTL'],
            'TotalDeliveryOrder' => $key['TotalDeliveryOrder'],
            'ExpiredDate' => $key['ExpiredDate'],
            'Hpp' => $nilaiHppFix,
            'HppTax' => $key['HppTax'],
            'KonversiQty' => $key['KonversiQty'],
            'Konversi_QtyTotal' => $key['Konversi_QtyTotal'],
            'Satuan_Konversi' => $key['Satuan_Konversi'],
            'NoBatch' => $key['NoBatch'],
            'DateEntry' => Carbon::now(),
            'UserEntry' =>  $key['UserEntry']
        ]);
    }
    public function editDeliveryOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrders')
            ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'TotalRowDO' => $request->TotalRowDO,
                'TotalQtyDO' => $request->TotalQtyDO,
                'Notes' => $request->Notes,
                'UserCreate' => $request->UserCreate,
                'UserEdit' => $request->UserEdit,
                'DateEdit' => Carbon::now(),
                'DeliveryOrderDate' => $request->DeliveryOrderDate,
                'Notes' => $request->Notes,
                'Notes1' => $request->Notes1,
                'Notes2' => $request->Notes2,
                'TotalQtyDelivery' => $request->TotalQtyDelivery,
                'SubtotalDelivery' => $request->SubtotalDelivery,
                'TaxDelivery' => $request->TaxDelivery,
                'GrandtotalDelivery' => $request->GrandtotalDelivery,
                'PurchaseOrderCode' => $request->PurchaseOrderCode,
                'UnitCode' => $request->UnitCode, 
                'JenisDelivery' => $request->JenisDelivery
            ]);
        return $updatesatuan;
    }

    public function getDeliveryOrderbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_hdr")
            ->where('TransactionCode', $id)
            ->get();
    }
    public function getDeliveryOrderDetailByBarang($ProductCode, $TransactionCode)
    {
        return  DB::connection('sqlsrv')->table("DeliveryOrderDetails")
        ->where('TransactionCode', $TransactionCode)
        ->where('ProductCode', $ProductCode)
        ->get(); 
    }
    public function updateDeliveryOrdeDetails($request,$key,$nilaiHppFix)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
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
                'SubtotalDeliveryOrder' => $key['SubtotalDeliveryOrder'],
                'TaxProsen' => $key['TaxProsen'],
                'TaxRp' => $key['TaxRp'],
                'TaxRpTTL' => $key['TaxRpTTL'],
                'TotalDeliveryOrder' => $key['TotalDeliveryOrder'],
                'ExpiredDate' => $key['ExpiredDate'],
                'NoBatch' => $key['NoBatch'],
                'Hpp' => $nilaiHppFix,
                'HppTax' => $key['HppTax'],
                'KonversiQty' => $key['KonversiQty'],
                'Konversi_QtyTotal' => $key['Konversi_QtyTotal'],
                'Satuan_Konversi' => $key['Satuan_Konversi'],
                'UserEntry' => $key['UserEntry'],
                'DateEntry' => $key[ 'DateEntry']
            ]);
        return $updatesatuan;
    }
    public function voidDeliveryOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrders')
            ->where('TransactionCode', $request->TransactionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidDeliveryOrderDetailAllOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
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
    public function voidDeliveryOrderDetailbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
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
        $ddatedmy = date("dmY", strtotime($request['DeliveryOrderDate']));
        return  DB::connection('sqlsrv')
            ->table('DeliveryOrders')
            ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from DeliveryOrders)"))->get();
    }
    public function getDeliveryOrderDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_Dtl")
            ->where('TransactionCode', $id)
            ->get();
    }
    public function getDeliveryOrderDetailbyIDandProductCode($key)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_Dtl")
        ->where('TransactionCode', $key->TransactionCode)
            ->where('ProductCode', $key->ProductCode)
            ->get();
    }
    public function getDeliveryOrderDetailbyIDandProductCodeLoop($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_Dtl")
        ->where('TransactionCode', $request->TransactionCode)
            ->where('ProductCode', $request->ProductCode)
            ->get();
    }
    public function getDeliveryOrderDetailbyIDandProductCodeLoopRetur($deliveryCode,$ProductCode)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_Dtl")
        ->where('TransactionCode', $deliveryCode)
            ->where('ProductCode', $ProductCode)
            ->get();
    }
    public function getDeliveryOrderDetailbyIDnotIdTrsNow($key,$request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_Dtl")
        ->where('TransactionCode','<>', $request->TransactionCode)
        ->where('ProductCode', $key->ProductCode)
        ->orderBy('ID', 'DESC')
        ->get();
    }
    public function getDeliveryOrderbyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_hdr")
            ->where('Usercreate', $request->UserCreate)
            ->get();
    }
    public function getDeliveryOrderbyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_do_hdr")
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
    public function getFakturbyIDDo($noDo)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_faktur_hdr")
        ->where('DeliveryCode', $noDo)
            ->get();
    }
    public function updateQtyRemainDeliveryOrder($request, $key, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
        ->where('TransactionCode', $request->TransactionOrderCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->update([
                'QtyDeliveryRemain' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function updateQtyRemainDeliveryOrderRetur($request, $key, $qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
        ->where('TransactionCode', $request->DeliveryCode)
            ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->update([
                'QtyDeliveryRemain' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function updateQtyRemainDeliveryOrderReturVoid($request, $qtyRemain,$productCode)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
        ->where('TransactionCode', $request->DeliveryCode)
            ->where('ProductCode', $productCode)
            ->where('Void', '0')
            ->update([
                'QtyDeliveryRemain' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function voidReturBelilbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('DeliveryOrderDetails')
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
}
