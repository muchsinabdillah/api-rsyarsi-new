<?php

namespace App\Http\Repository;
use Illuminate\Support\Facades\DB;

class aHnaRepositoryImpl implements aHnaRepositoryInterface
{
    public function addHna($request, $key,$nilaiHnaFix,$hnaTaxDiskon)
    {
        $dateStart = date('Y-m-d', strtotime($request->TransactionDate . ' + 1 days'));
        return  DB::connection('sqlsrv')->table("Hnas")->insert([
            'DeliveryCode' => $request->TransactionCode,
            'DeliveryDate' => $request->TransactionDate,
            'ProductCode' => $key['ProductCode'],
            'NominalHna' => $nilaiHnaFix,
            'NominalHnaMinDiskon' => $hnaTaxDiskon,
            'UserCreate' => $request->UserCreate,
            'StartDate' => $dateStart,
            'ExpiredDate' => "4000-01-01"
        ]);
    }
    public function updateHna($request, $key,$nilaiHnaFix,$hnaTaxDiskon)
    { 
        $dateStart = date('Y-m-d', strtotime($request->TransactionDate . ' + 1 days'));
        return DB::connection('sqlsrv')->table('Hnas')
        ->where('DeliveryCode', $request->TransactionCode)
        ->where('Batal','0')
        ->where('ProductCode', $key['ProductCode'])
            ->update(['DeliveryDate' => $request->TransactionDate,
            'ProductCode' => $key['ProductCode'],
            'NominalHna' => $nilaiHnaFix,
            'NominalHnaMinDiskon' => $hnaTaxDiskon,
            'UserCreate' => $request->UserCreate,
            'StartDate' => $dateStart,
            'ExpiredDate' => "4000-01-01"
            ]);
    }
    // public function getHnabyDOIdbarang($request,$key)
    // {
    //     return  DB::connection('sqlsrv')->table("Hnas")
    //     ->where('DeliveryCode', $key['ProductCode'])
    //     ->where('ProductCode', $request->ProductCode)
    //     ->get();
    // }
    // public function getHnabyIdbarang($request)
    // {
    //     return  DB::connection('sqlsrv')->table("Hnas") 
    //     ->where('ProductCode', $request->ProductCode)
    //         ->get();
    // }
    public function getHpp($key,$request){
        return  DB::connection('sqlsrv')->table("hpps") 
        ->select('NominalHpp')
        ->where('Batal','0')
        ->where('ProductCode', $key['ProductCode'])
        ->where('DeliveryCode','<>', $request->TransactionCode)
        ->orderBy('id','desc')
        ->get()->chunk(1);
    }
    public function getHnaHigh($key,$request){
        return  DB::connection('sqlsrv')->table("Hnas") 
        ->where('ProductCode', $key['ProductCode'])
        ->where('DeliveryCode','<>', $request->TransactionCode)
        ->where('Batal','0')
        ->orderBy('NominalHna','desc')
        ->get()->chunk(1);
    }
    public function getHnaHighPeriodik($ProductCode,$tgl)
    {
        return  DB::connection('sqlsrv')->table("Hnas") 
        ->where('ProductCode',$ProductCode) 
        ->where('Batal','0')
        ->whereRaw("'$tgl' BETWEEN StartDate AND ExpiredDate")
        ->orderBy('NominalHna','desc')
        ->get()->chunk(1);
    }
    public function getHppAverage($key){
        return  DB::connection('sqlsrv')->table("hpps") 
        ->select('NominalHpp')
        ->where('Batal','0')
        ->where('ProductCode', $key['ProductCode'])
        ->orderBy('id','desc')
        ->get()->chunk(1);
    }
    public function getHppAverageV2($product){
        return  DB::connection('sqlsrv')->table("hpps") 
        ->select('NominalHpp')
        ->where('Batal','0')
        ->where('ProductCode', $product)
        ->orderBy('id','desc')
        ->get()->chunk(1);
    }
    public function getHppAveragebyCode($ProductCode){
        return  DB::connection('sqlsrv')->table("hpps") 
        ->select('NominalHpp')
        ->where('Batal','0')
        ->where('ProductCode', $ProductCode)
        ->orderBy('id','desc')
        ->get()->chunk(1);
    }
    public function addHpp($request,$key,$nilaiHppFix){
        return  DB::connection('sqlsrv')->table("hpps")->insert([
            'DeliveryCode' =>  $request->TransactionCode,  
            'NominalHargabeli' =>  $key['Price']/$key['KonversiQty'],
            'ProductCode' =>  $key['ProductCode'],
            'NominalDiskon' =>  $key['DiscountRp']/$key['KonversiQty'],
            'DeliveryDate' => $request->TransactionDate,
            'NominalHpp' => $nilaiHppFix,
            'UserCreate' =>  $request->UserCreate, 
        ]);
    }
    
}
