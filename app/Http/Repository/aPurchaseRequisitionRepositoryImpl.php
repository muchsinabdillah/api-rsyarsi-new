<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class aPurchaseRequisitionRepositoryImpl implements aPurchaseRequisitionRepositoryInterface
{
    public function addPurchaseRequisition($request,$autoNumber)
    { 
        return  DB::connection('sqlsrv')->table("PurchaseRequisitions")->insert([
            'TransasctionDate' => $request->TransasctionDate,
            'UserCreate' => $request->UserCreate,
            'Type' => $request->Type,
            'Unit' => $request->Unit,
            'Notes' => $request->Notes,
            'TransactionCode' => $autoNumber,
            'DateCreateReal' => Carbon::now(),
            'UseCreateReal' => $request->UserCreate,
            'ReffDateTrs' => date("dmY", strtotime($request->TransasctionDate))
        ]);
    }
    public function addPurchaseRequisitionDetil($request)
    {
        return  DB::connection('sqlsrv')->table("PurchaseRequisitionDetails")->insert([
            'TransactionCode' => $request->TransasctionCode,
            'ProductCode' => $request->ProductCode,
            'ProductName' => $request->ProductName,
            'QtyStok' => $request->QtyStok,
            'QtyPR' => $request->QtyPR,
            'QtyRemainPR' => $request->QtyPR,
            'DateAdd' => Carbon::now(),
            'Satuan' =>  $request->Satuan,
            'Satuan_Konversi' =>  $request->Satuan_Konversi,
            'KonversiQty' =>  $request->KonversiQty,
            'Konversi_QtyTotal' =>  $request->Konversi_QtyTotal,
            'UserAdd' =>  $request->UserAdd
        ]);
    }
    public function editPurchaseRequisition($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitions')
            ->where('TransactionCode', $request->TransasctionCode)
            ->update([
                'TotalQty' => $request->TotalQty,
                'TotalRow' => $request->TotalRow,
                'Notes' => $request->Notes,
                'UserCreate' => $request->UserCreate,
                'TransasctionDate' => $request->TransactionDate
            ]);
        return $updatesatuan;
    }

    public function getPurchaseRequisitionbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_pr_hdr")
        ->where('TransactionCode', $id)
        ->where('Void', '0')
        ->get();
    }
    public function getPurchaseRequisitionApprovedbyID($id)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_pr_hdr")
        ->where('TransactionCode', $id)
            ->where('Void', '0')
            ->where('Approved', '1')
            ->get();
    }
    public function getItemsDouble($request)
    {
        return  DB::connection('sqlsrv')->table("PurchaseRequisitionDetails")
        ->where('TransactionCode', $request->TransasctionCode)
            ->where('Void', '0')
            ->where('ProductCode', $request->ProductCode)
            ->get();
    } 
    public function voidPurchaseRequisition($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitions')
        ->where('TransactionCode', $request->TransasctionCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid 
            ]);
        return $updatesatuan;
    }
    public function voidPurchaseRequisitionDetailbyItem($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitionDetails')
        ->where('TransactionCode', $request->TransasctionCode)
        ->where('ProductCode', $request->ProductCode)
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid
            ]);
        return $updatesatuan;
    }
    public function voidPurchaseRequisitionDetailAll($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitionDetails')
        ->where('TransactionCode', $request->TransasctionCode)
            ->where('Void', '0')
            ->update([
                'Void' => $request->Void,
                'DateVoid' => $request->DateVoid,
                'UserVoid' => $request->UserVoid
            ]);
        return $updatesatuan;
    }
    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['TransasctionDate']));
        return  DB::connection('sqlsrv')
        ->table('PurchaseRequisitions')
        ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`TransactionCode`) from PurchaseRequisitions)"))->get();
    }
    public function getPurchaseRequisitionDetailbyID($id)
    {
        return  DB::connection('sqlsrv')->table("PurchaseRequisitionDetails")
        ->where('TransactionCode', $id)
        ->where('Void', '0')
        ->get();
    }
    public function getPurchaseRequisitionDetailbyIDBarang($request,$key)
    {
        return  DB::connection('sqlsrv')->table("PurchaseRequisitionDetails")
        ->where('TransactionCode', $request->PurchaseReqCode)
        ->where('ProductCode', $key['ProductCode'])
        ->where('Void', '0')
        ->get();
    }
    public function getPurchaseRequisitionDetailPObyIDBarang($request)
    {
        return  DB::connection('sqlsrv')->table("PurchaseRequisitionDetails")
        ->where('TransactionCode', $request->PurchaseRequisitonCode)
        ->where('ProductCode', $request->ProductCode)
        ->where('Void', '0')
        ->get();
    }
    public function getPurchaseRequisitionDetailPObyIDBarang2($PurchaseRequisitonCode, $ProductCode)
    {
        return  DB::connection('sqlsrv')->table("PurchaseRequisitionDetails")
        ->where('TransactionCode', $PurchaseRequisitonCode)
        ->where('ProductCode', $ProductCode)
        ->where('Void', '0')
        ->get();
    }
    public function updateQtyRemainPR($request,$key,$qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitionDetails')
        ->where('TransactionCode', $request->PurchaseReqCode)
         ->where('ProductCode', $key['ProductCode'])
            ->where('Void', '0')
            ->update([
                'QtyRemainPR' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function updateQtyRemainPRbyPo($request,$qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitionDetails')
        ->where('TransactionCode', $request->PurchaseRequisitonCode)
         ->where('ProductCode', $request->ProductCode)
            ->where('Void', '0')
            ->update([
                'QtyRemainPR' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function updateQtyRemainPRbyPo2($PurchaseRequisitonCode,$ProductCode,$qtyRemain)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitionDetails')
        ->where('TransactionCode', $PurchaseRequisitonCode)
         ->where('ProductCode', $ProductCode)
            ->where('Void', '0')
            ->update([
                'QtyRemainPR' => $qtyRemain
            ]);
        return $updatesatuan;
    }
    public function getPurchaseRequisitionbyDateUser($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_pr_hdr")
            ->where('Usercreate', $request->UserCreate)
            ->where('Void', '0')
            ->get();
    }
    public function getPurchaseRequisitionbyPeriode($request)
    {
        return  DB::connection('sqlsrv')->table("v_transaksi_pr_hdr")
        ->whereBetween('TglPeriode', [$request->StartPeriode, $request->EndPeriode] )
            ->where('Void', '0')
            ->get();
    }
    public function approvalPR($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('PurchaseRequisitions')
        ->where('TransactionCode', $request->PurchaseRequisitonCode)  
            ->update([
                'Approved' => '1',
                'UserApproved' => $request->UserApprove,
                'DateApproved' => $request->DateApprove
            ]);
        return $updatesatuan;
    }

    public function getPurchaseRequisitionQtyRemain($id)
    {
        return  DB::connection('sqlsrv')->table("PurchaseRequisitionDetails")
        ->where('TransactionCode', $id)
        ->where('Void', '0')
        ->where('QtyRemainPR','<>', '0')
        //->select(DB::raw('sum(QtyRemainPR) as totalQtyRemainPR'))
        ->select(DB::raw('count(QtyRemainPR) as totalQtyRemainPR'))
        ->get();
    }
}
