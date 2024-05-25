<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class aBookingBedRepositoryImpl implements aBookingBedRepositoryInterface
{
    public function createTrs($request,$autonumber)
    { 
        return  DB::connection('sqlsrv9')->table("BookingBeds")->insert([
            'transactioncode' => $autonumber,
            'transactiondate' => $request->transactiondate,
            'bookingbeddate' => $request->bookingbeddate,
            'medicalrecordnumber' => $request->medicalrecordnumber,
            'patientname' => $request->patientname,
            'patientaddress' => $request->patientaddress,
            'patientsex' => $request->patientsex,
            'patientbirthplace' => $request->patientbirthplace,
            'patientbirthdate' => $request->patientbirthdate,
            'classid' => $request->classid,
            'classname' => $request->classname,
            'roomid' => $request->roomid,
            'roomname' => $request->roomname,
            'bedid' => $request->bedid,
            'bedname' => $request->bedname,
            'notes' => $request->notes,
            'bookingstatus' => '0',
            'userentri' => $request->userentri,
            'dateentri' => Carbon::now(),
            'ReffDateTrs' => date("dmY", strtotime($request->transactiondate)),
            'jenisbooking' => $request->jenisbooking,
            'jenispasien' => $request->jenispasien,
        ]);
    }
    public function editTrs($request)
    { 
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->where('transactioncode',$request->transactioncode)
        ->update([
            'bookingbeddate' => $request->bookingbeddate,
            'medicalrecordnumber' => $request->medicalrecordnumber,
            'patientname' => $request->patientname,
            'patientaddress' => $request->patientaddress,
            'patientsex' => $request->patientsex,
            'patientbirthplace' => $request->patientbirthplace,
            'patientbirthdate' => $request->patientbirthdate,
            'classid' => $request->classid,
            'classname' => $request->classname,
            'roomid' => $request->roomid,
            'roomname' => $request->roomname,
            'bedid' => $request->bedid,
            'bedname' => $request->bedname,
            'notes' => $request->notes,
            'bookingstatus' => '0',
            'userupdate' => $request->userupdate,
            'dateupdate' => Carbon::now(),
            'jenisbooking' => $request->jenisbooking,
        ])
        ;
    }

    public function voidTrs($request)
    { 
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->where('transactioncode',$request->transactioncode)
        ->update([
            'uservoid' => $request->uservoid,
            'datevoid' => Carbon::now(),
            'void' => '1',
        ])
        ;
    }

    public function getMaxCode($request)
    {
        $ddatedmy = date("dmY", strtotime($request['transactiondate']));
        return  DB::connection('sqlsrv9')
        ->table('BookingBeds')
        ->where('ReffDateTrs', $ddatedmy, DB::raw("(select max(`transactioncode`) from BookingBeds)"))->get();
    } 
   
    public function getTrsBookingBedByTrsCode($id)
    {
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->where('transactioncode',$id)
        ->get();
    }

    public function getTrsBookingBedByTrsCodeActive($id)
    {
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->where('transactioncode',$id)
        ->where('void', '0')
        ->select('*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as tglbooking"),DB::raw("CASE WHEN bookingstatus='0' then 'OPEN' ELSE 'CLOSED' END AS StatusName"),DB::raw("replace(CONVERT(VARCHAR(11), patientbirthdate, 111), '/','-') as patientbirthdate"))
        ->get();
    }

    public function getTrsBookingBedByMRActiveSameDay($request)
    {
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->where('bookingstatus', '0')
        ->where('void', '0')
        ->where('medicalrecordnumber', $request->medicalrecordnumber)
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-')"), $request->bookingbeddate)
        ->get();
    }

    public function getListBookingBedActiveByPeriode($request)
    {
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->select(['*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as bookingbeddate") ]) 
        ->where('void','0')
        ->where('bookingstatus','0')
        ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-')"), [$request->StartPeriode,$request->EndPeriode]) 
        ->get();
    }
    public function getListBookingBedArchiveByPeriode($request)
    {
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->select(['*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as bookingbeddate") ]) 
        ->where('void','0')
        ->where('bookingstatus','1')
        ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-')"), [$request->StartPeriode,$request->EndPeriode]) 
        ->get();
    }
    public function getListBookingBedActiveByNoMR($request)
    {
        return  DB::connection('sqlsrv9')->table("BookingBeds")
        ->select(['*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as bookingbeddate") ]) 
        ->where('void','0')
        ->where('bookingstatus','0')
        ->where('medicalrecordnumber',$request->medicalrecordnumber)
        ->get();
    }
    public function getAvailableBed($request)
    {
        return  DB::connection('sqlsrv2')->table("MstrRoomID")
        ->where('RoomID', $request->bedid)
        ->where('Status','0')
        ->get();
    }
    public function updateStatusMasterBedTerbooking($bedid)
    { 
        return  DB::connection('sqlsrv2')->table("MstrRoomID")
        ->where('RoomID', $bedid)
        ->update([
            'Status' => '2',
        ])
        ;
    }
    public function updateStatusMasterBedTersedia($bedid)
    { 
        return  DB::connection('sqlsrv2')->table("MstrRoomID")
        ->where('RoomID', $bedid)
        ->update([
            'Status' => '0',
        ])
        ;
    }
}
