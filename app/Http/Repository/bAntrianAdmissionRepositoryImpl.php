<?php

namespace App\Http\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Repository\bAntrianAdmissionRepositoryInterface;

class bAntrianAdmissionRepositoryImpl implements bAntrianAdmissionRepositoryInterface
{
    public function CreateAntrianAdmission($request,$noAntrian,$datenow,$autonumber)
    {
        return  DB::connection('sqlsrv3')->table("AntrianListAdmission")->insert([
            'NoAntrian' =>  $noAntrian, 
            'NoAntrianAll' =>  $autonumber,     
            'DateCreated' => $datenow,  
            'FloorId' =>  $request->FloorID, 
            'Status' =>  "CREATED",
            'JenisJaminan' =>  $request->Jenis_Jaminan 
        ]);
    }
    public function CreateAntrianAdmissionAPM($request,$noAntrian,$datenow,$autonumber)
    {
        return  DB::connection('sqlsrv3')->table("AntrianListAdmissionAPM")->insert([
            'NoAntrian' =>  $noAntrian, 
            'NoAntrianAll' =>  $autonumber,     
            'DateCreated' => $datenow,  
            'FloorId' =>  $request->FloorID, 
            'Status' =>  "CREATED",
            'JenisJaminan' =>  $request->Jenis_Jaminan 
        ]);
    }
    public function getMaxAntrianAdmissionAPM($tglAntrian){
        return  DB::connection('sqlsrv3')->table("AntrianListAdmissionAPM")
        ->select('NoAntrianAll')
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), DateCreated, 111), '/','-')"),$tglAntrian)   
        ->where('Batal', '0')
        ->orderBy('NoAntrianAll', 'desc')->first();
    } 
    public function getMaxAntrianAdmission($tglAntrian,$jenisJaminan){
        return  DB::connection('sqlsrv3')->table("AntrianListAdmission")
        ->select('NoAntrianAll')
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), DateCreated, 111), '/','-')"),$tglAntrian)   
        ->where('JenisJaminan', $jenisJaminan)
        ->where('Batal', '0')
        ->orderBy('NoAntrianAll', 'desc')->first();
    } 
    // public function ListAntrianAdmission($request)
    // {
    //     return  DB::connection('sqlsrv3')->table("AntrianListAdmission")
    //         ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), DateSavedResep, 111), '/','-')"),[$request->StartPeriode, $request->EndPeriode])   
    //         ->where('IDUnitFarmasi', $request->IdUNitFarmasi)
    //         ->where('Batal', '0')
    //         ->whereIn('StatusAntrean', ['CREATED','PROCESSED'])
    //         ->orderBy('DateCreated', 'ASC')
    //         ->get();
    // }
    public function PanggilAntrian($request,$datenow)
    {
        $updatesatuan =  DB::connection('sqlsrv3')->table('AntrianListAdmission')
        ->where('ID', $request->IDTrsAntrian) 
            ->update([
                'UserId' => $request->Username, 
                'Status' =>  "CALLED",
                'DateCalled' => $datenow
            ]);
        return $updatesatuan;
    }
    public function HoldAntrian($request,$datenow)
    {
        $updatesatuan =  DB::connection('sqlsrv3')->table('AntrianListAdmission')
        ->where('ID', $request->IDTrsAntrian) 
            ->update([
                'UserId' => $request->Username, 
                'Status' =>  "HOLD",
                'DateHold' => $datenow
            ]);
        return $updatesatuan;
    }
    public function ProccesedAntrian($request,$datenow)
    {
        $updatesatuan =  DB::connection('sqlsrv3')->table('AntrianListAdmission')
        ->where('ID', $request->IDTrsAntrian) 
            ->update([
                'UserId' => $request->Username, 
                'Status' =>  "PROCCESSED",
                'DateProccessed' => $datenow
            ]);
        return $updatesatuan;
    }
    public function ClosedAntrian($request,$datenow)
    {
        $updatesatuan =  DB::connection('sqlsrv3')->table('AntrianListAdmission')
        ->where('ID', $request->IDTrsAntrian) 
            ->update([
                'UserId' => $request->Username, 
                'Status' =>  "CLOSED",
                'DateClosed' => $datenow
            ]);
        return $updatesatuan;
    }
    public function getAntrianAdmissionbyID($idTrs){
        return  DB::connection('sqlsrv3')->table("AntrianListAdmission")
        ->select('NoAntrianAll') 
        ->where('Id', $idTrs)
        ->where('Batal', '0')->get();
    } 
    
    public function ViewbyIdTrsAntrianAdmission($id)
    {
        return  DB::connection('sqlsrv3')->table("AntrianListAdmission") 
        ->select('Id','NoAntrian','Status','FloorId') 
        ->where('Id', $id)  
        ->get();
    }
    public function ViewbyDateTrsAntrianAdmission($request)
    {
        return  DB::connection('sqlsrv3')->table("AntrianListAdmission") 
        ->select('Id','NoAntrian','Status','FloorId') 
        ->where('Status', '<>','CLOSED')
        ->whereBetween('DateCreated', [$request->StartPeriode, $request->EndPeriode])
        ->orderBy('Id', 'asc')
        ->get();
    }
    public function ViewbyFloorTrsAntrianAdmission($request)
    {
        return  DB::connection('sqlsrv3')->table("AntrianListAdmission") 
        ->select('Id','NoAntrian','Status','FloorId') 
        ->where('FloorId', $request->FloorId)
        ->where('Batal', '0')
        ->where('Status', '<>','CLOSED')
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), DateCreated, 111), '/','-')"),$request->DateCreated)    
        ->orderBy('Id', 'asc')
        ->get();
    }
    public function ViewbyDateTrsJaminanAntrianAdmission($request)
    {
        return  DB::connection('sqlsrv3')->table("AntrianListAdmission") 
        ->select('Id','NoAntrian','Status','FloorId')  
        ->whereBetween('DateCreated', [$request->StartPeriode, $request->EndPeriode])
        ->where('JenisJaminan', $request->JenisJaminan) 
        ->where('Status', '<>','CLOSED') 
        ->orderBy('Id', 'asc')
        ->get();
    }
}