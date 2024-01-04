<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class aTrsResepRepositoryImpl implements aTrsResepRepositoryInterface
{

    public function viewResepHeader($idResep)
    {
        return  DB::connection('sqlsrv')->table("Orders")
        ->where('Order ID', $idResep)
        ->select(DB::raw("[Order ID] as ID"), DB::raw("Text as Description_data"))
        ->get();
    }
    
    public function viewResepDetail($idResep)
    {
        return  DB::connection('sqlsrv')->table("Order Details")
        ->where('Order ID', $idResep)
        ->select('NamaObat','Quantity','QtyRealisasi','Signa','Note1','Review','Dosis',DB::raw("[Product ID] as IdBarang"))
        ->get();
    }
    public function viewOrderReseV2pbyDatePeriode($request)
    {
        // $query = DB::connection('sqlsrv')->table("ResepV2_ViewbyPeriodeDate")
        // ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), TglResep, 111), '/','-')"),
        // [$request->tglPeriodeAwal,$request->tglPeriodeAkhir]) 
        $query =  DB::connection('sqlsrv')->table("ResepV2_ViewbyPeriodeDate")
        ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), TglResep, 111), '/','-')"),
        [$request->tglPeriodeAwal,$request->tglPeriodeAkhir])  
        ->get();
        return $query;
    }

    public function viewOrderResepbyOrderIDV2($request){
        $query = DB::connection('sqlsrv')->table("v_transaksi_orderresep_hdr")
        ->where('OrderID',$request)
       // ->where('Batal','0')
        ->get();
        return $query;
    }

    public function viewOrderResepDetailbyOrderIDV2($request){
        $query = DB::connection('sqlsrv')->table("v_transaksi_orderresep_dtl")
        ->where('IdOrderResep',$request)
        ->orderBy('Racik','asc')
        ->get();
        return $query;
    }

    public function editSignaTerjemahanbyID($ID,$AturanPakai)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderResepDetail')
        ->where('ID', $ID)
            ->update([
                'SignaTerjemahan' => $AturanPakai,
            ]);
        return $updatesatuan;
    }

    public function viewprintLabelbyID($request){
        $query = DB::connection('sqlsrv')->table("v_label_orderresep_dtl")
        ->where('IDDetail',$request)
        ->orderBy('Racik','asc')
        ->get();
        return $query;
    }

    public function getPrinterLabel($request){
        $query = DB::connection('sqlsrv2')->table("SharingPrinter")
        ->where('IP_Komputer',$request->ipkomputer)
        ->where('Jenis',$request->signaobat)
        ->get();
        return $query;
    }

    public function editReviewbyIDResep($request)
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('OrderResepDetail')
        ->where('IdOrderResep', $request)
        ->where('Batal', '0')
            ->update([
                'Review' => '1',
            ]);
        return $updatesatuan;
    }
    
}
