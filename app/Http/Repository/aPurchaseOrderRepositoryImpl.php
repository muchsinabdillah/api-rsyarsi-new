<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aPurchaseOrderRepositoryImpl implements aPurchaseOrderRepositoryInterface
{
    public function addPurchaseOrder($request, $autoNumber)
    {
        return  DB::connection('sqlsrv')->table("PurchaseOrders")->insert([
            'PurchaseDate' => $request->PurchaseDate,
            'UserCreate' => $request->UserCreate,
            'SupplierCode' => $request->SupplierCode,
            'Notes' => $request->Notes, 
            'PurchaseReqCode' => $request->PurchaseReqCode,
            'TipePO' => $request->TipePO,
            'PurchaseCode' => $autoNumber,
            'DateCreateReal' => Carbon::now(),
            'UseCreateReal' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->PurchaseDate))
        ]);
    }
    public function addPurchaseOrderDetil($key, $kodePo)
    {
        return  DB::connection('sqlsrv')->table("PurchaseOrderDetails")->insert([
            'PurchaseCode' => $kodePo,
            'ProductCode' => $key['ProductCode'],
            'ProductName' => $key['ProductName'],
            'ProductSatuan' => $key['ProductSatuan'],
            'LastPrice' => $key['LastPrice'],
            'Price' => $key['Price'],
            'DiscountProsen' => $key['DiscountProsen'],
            'DiscountRp' => $key['DiscountRp'],
            'DiscountRpTTL' => $key['DiscountRpTTL'],
            'QtyPurchase' => $key['QtyPurchase'], // tambahan qty pr
            'QtyPR' => $key['QtyPurchase'],
            'QtyPurchaseRemain' => $key['QtyPurchaseRemain'],
            'SubtotalPurchase' => $key['SubtotalPurchase'],
            'TaxProsen' => $key['TaxProsen'],
            'TaxRp' => $key['TaxRp'],
            'TaxRpTTL' => $key['TaxRpTTL'],
            'TotalPurchase' => $key['TotalPurchase'],
            'QtyStok' => $key['QtyStok'], 
            'QtyPurchaseRemain' => $key['QtyPR'],
            'DateEntry' => Carbon::now(),
            'UserEntry' =>  $key['UserEntry']
        ]);
    }
    public function editPurchaseOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrders')
            ->where('PurchaseCode', $request->PurchaseCode)
            ->update([ 
                'TotalRowPO' => $request->TotalRowPO,
                'TotalQtyPO' => $request->TotalQtyPO,
                'Notes' => $request->Notes,
                'UserCreate' => $request->UserCreate,
                'UserEdit' => $request->UserEdit,
                'DateEdit' => Carbon::now(),
                'PurchaseDate' => $request->PurchaseDate,
                'Notes' => $request->Notes,
                'Notes1' => $request->Notes1,
                'Notes2' => $request->Notes2,
                'TotalQtyPurchase' => $request->TotalQtyPurchase,
                'SubtotalPurchase' => $request->SubtotalPurchase,
                'TaxPurchase' => $request->TaxPurchase,
                'GrandtotalPurchase' => $request->GrandtotalPurchase,
                'PurchaseReqCode' => $request->PurchaseReqCode,
                'Close_PO' => $request->Close_PO,
                'TotalRowPO' => $request->TotalRowPO,
                'TipePO' => $request->TipePO 
            ]);
        return $updatesatuan;
    }

    public function getPurchaseOrderbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_po_hdr")
            ->where('PurchaseCode', $id) 
            ->get();
    }
    public function getPurchaseOrderApprovedbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_po_hdr")
            ->where('PurchaseCode', $id)
            ->where('UserApproved_2', '<>','')
            ->get();
    }
    public function getItemsDouble($request)
    {
        return  DB::connection('sqlsrv')->table("PurchaseOrderDetails")
            ->where('TransactionCode', $request->TransasctionCode)
            ->where('Void', '0')
            ->where('ProductCode', $request->ProductCode)
            ->get();
    }
    public function voidPurchaseOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrders')
            ->where('PurchaseCode', $request->PurchaseCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidPurchaseDetailAllOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrderDetails')
        ->where('PurchaseCode', $request->PurchaseCode)
        ->where('Void', "0")
            ->update([
                'Void' => $request->Void,
                'DateVoid' => Carbon::now(),
                'UserVoid' => $request->UserVoid,
                'ReasonVoid' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidPurchaseOrderDetailbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrderDetails')
        ->where('PurchaseCode', $request->PurchaseCode)
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
        $ddatedmy = date("dmY", strtotime($request['PurchaseDate']));
        return  DB::connection('sqlsrv')
            ->table('PurchaseOrders')
            ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from PurchaseOrders)"))->get();
    }
    public function getPurchaseOrderDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_po_dtl")
            ->where('PurchaseCode', $id)
            ->where('Void', '0')
            ->get();
    }
    public function getPurchaseOrderbyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_po_hdr")
            ->where('Usercreate', $request->UserCreate)
            ->get();
    }
    public function getPurchaseOrderbyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_po_hdr")
        ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode])
        ->get();
    }
    public function delItemsbyPOnumber($key,$po)
    {
       
        return  DB::connection('sqlsrv')->table("PurchaseOrderDetails")
            ->where('ProductCode', $key['ProductCode'])
            ->where('PurchaseCode', $po)
            ->delete();
    }
    public function editQtyPurchaseRemain($request, $qtyremainPO,$productcode)
    { 
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrderDetails')
        ->where('PurchaseCode', $request->PurchaseCode)
        ->where('ProductCode', $productcode)
            ->update([
                'QtyPurchaseRemain' => $qtyremainPO
            ]);
        return $updatesatuan;
    }
    public function getPurchaseOrderDetailbyIDBrgForDo($request,$key)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_po_dtl")
        ->where('PurchaseCode', $request->PurchaseCode)
        ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->get();
    }
    public function getPurchaseOrderDetailbyIDBrgForDo2($request, $key)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_po_dtl")
        ->where('PurchaseCode', $request->PurchaseCode)
            ->where('ProductCode', $key)
            ->where('Void', '0')
            ->get();
    }
    public function approvalFirst($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrders')
        ->where('PurchaseCode', $request->PurchaseCode)  
            ->update([ 
                'UserApproved_1' => $request->UserApprove,
                'DateApproved_1' => $request->DateApprove
            ]);
        return $updatesatuan;
    }
    public function approvalSecond($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrders')
        ->where('PurchaseCode', $request->PurchaseCode)  
            ->update([ 
                'UserApproved_2' => $request->UserApprove,
                'DateApproved_2' => $request->DateApprove
            ]);
        return $updatesatuan;
    }
    public function approvalThirth($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrders')
        ->where('PurchaseCode', $request->PurchaseCode)  
            ->update([ 
                'UserApproved_3' => $request->UserApprove,
                'DateApproved_3' => $request->DateApprove
            ]);
        return $updatesatuan;
    }
    public function closePurchaseOrder($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseOrders')
            ->where('PurchaseCode', $request->PurchaseCode)
            ->update([
                'ClosePO' => $request->Close,
                'DateClosePO' => Carbon::now(),
                'UserClosePO' => $request->UserClose,
                'ReasonClosePO' => $request->ReasonClose
            ]);
        return $updatesatuan;
    }
}

