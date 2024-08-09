<?php

namespace App\Http\Repository;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class bPaymentRepositoryImpl implements bPaymentRepositoryInterface
{
    public function createHdr($request,$nofinalkwitansi)
    { 
        return  DB::connection('sqlsrv3')->table("payments")->insertGetId([
            'NoKwitansi' => $nofinalkwitansi,
            'NoEpisode' => $request->NoEpisode,
            'NoRegistrasi' => $request->NoRegistrasi,
            'Paymentdate' => $request->Paymentdate,
            'Jam' => Carbon::now(),
            'TotalPaid' => $request->TotalPaid,
            'Ammount' => $request->Ammount,
            'PendapatanPoli' => '0',
            'PendapatanApotik' => $request->PendapatanApotik,
            'PendapatanRadiologi' => $request->PendapatanRadiologi,
            'PendapatanLab' => $request->PendapatanLab,
            'Kasir' => $request->Kasir,
            'Billto' => $request->Billto,
            'Descripton' => $request->Descripton,
            'Id_Kasir' => $request->Id_Kasir,
        ]);
    }

    public function createDtl($request,$getlastid)
    { 
        return  DB::connection('sqlsrv3')->table("PaymentDetails")
        ->insert([
            'PaymentID' => $getlastid,
            'NoRegistrasi' => $request->NoRegistrasi,
            'TipePembayaran' => $request->TipePembayaran,
            'Tgl' => Carbon::now(),
            'TotalPaid' => $request->TotalPaid,
        ]);
    }

    public function getPaymentByNoReg($noreg)
    {
        return  DB::connection('sqlsrv3')->table("payments")
        ->where('NoRegistrasi',$noreg)
        ->get();
    }

    public function getMaxCode($paymentdate,$kdawal)
    {
        return  DB::connection('sqlsrv3')
        ->table('payments')
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), Paymentdate, 111), '/','-')"),$paymentdate)
        ->where(DB::raw("LEFT(NoKwitansi,3)"),$kdawal)
        ->orderBy('Id','DESC')
        ->select(DB::raw("TOP 1 right(NoKwitansi,4) as urutkwitansi"))
        ->get();
    } 

    public function closeBill($request)
    { 
        return  DB::connection('sqlsrv3')->table("visit")
        ->where('NoRegistrasi',$request->NoRegistrasi)
        ->update([
            'Status ID' => '4',
            'lock' => '1',
            'First_date_close' => Carbon::now(),
            'First_user_close' => $request->Id_Kasir,
        ]);
    }

    public function closeBillDataRWJ($request)
    { 
        return  DB::connection('sqlsrv6')->table("dataRWJ")
        ->where('NoRegistrasi',$request->NoRegistrasi)
        ->update([
            'StatusID' => '4',
        ]);
    }
   
    // public function getTrsBookingBedByTrsCode($id)
    // {
    //     return  DB::connection('sqlsrv9')->table("BookingBeds")
    //     ->where('transactioncode',$id)
    //     ->get();
    // }

    // public function getTrsBookingBedByTrsCodeActive($id)
    // {
    //     return  DB::connection('sqlsrv9')->table("BookingBeds")
    //     ->where('transactioncode',$id)
    //     ->where('void', '0')
    //     ->select('*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as tglbooking"),DB::raw("CASE WHEN bookingstatus='0' then 'OPEN' ELSE 'CLOSED' END AS StatusName"),DB::raw("replace(CONVERT(VARCHAR(11), patientbirthdate, 111), '/','-') as patientbirthdate"))
    //     ->get();
    // }

    // public function getTrsBookingBedByMRActiveSameDay($request)
    // {
    //     return  DB::connection('sqlsrv9')->table("BookingBeds")
    //     ->where('bookingstatus', '0')
    //     ->where('void', '0')
    //     ->where('medicalrecordnumber', $request->medicalrecordnumber)
    //     ->where(DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-')"), $request->bookingbeddate)
    //     ->get();
    // }

    // public function getListBookingBedActiveByPeriode($request)
    // {
    //     return  DB::connection('sqlsrv9')->table("BookingBeds")
    //     ->select(['*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as bookingbeddate") ]) 
    //     ->where('void','0')
    //     ->where('bookingstatus','0')
    //     ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-')"), [$request->StartPeriode,$request->EndPeriode]) 
    //     ->get();
    // }
    // public function getListBookingBedArchiveByPeriode($request)
    // {
    //     return  DB::connection('sqlsrv9')->table("BookingBeds")
    //     ->select(['*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as bookingbeddate") ]) 
    //     ->where('void','0')
    //     ->where('bookingstatus','1')
    //     ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-')"), [$request->StartPeriode,$request->EndPeriode]) 
    //     ->get();
    // }
    // public function getListBookingBedActiveByNoMR($request)
    // {
    //     return  DB::connection('sqlsrv9')->table("BookingBeds")
    //     ->select(['*', DB::raw("replace(CONVERT(VARCHAR(11), bookingbeddate, 111), '/','-') as bookingbeddate") ]) 
    //     ->where('void','0')
    //     ->where('bookingstatus','0')
    //     ->where('medicalrecordnumber',$request->medicalrecordnumber)
    //     ->get();
    // }
    // public function getAvailableBed($request)
    // {
    //     return  DB::connection('sqlsrv2')->table("MstrRoomID")
    //     ->where('RoomID', $request->bedid)
    //     ->where('Status','0')
    //     ->get();
    // }
    // public function updateStatusMasterBedTerbooking($bedid)
    // { 
    //     return  DB::connection('sqlsrv2')->table("MstrRoomID")
    //     ->where('RoomID', $bedid)
    //     ->update([
    //         'Status' => '2',
    //     ])
    //     ;
    // }
    // public function updateStatusMasterBedTersedia($bedid)
    // { 
    //     return  DB::connection('sqlsrv2')->table("MstrRoomID")
    //     ->where('RoomID', $bedid)
    //     ->update([
    //         'Status' => '0',
    //     ])
    //     ;
    // }
}
