<?php

namespace App\Http\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\bPaymentRepositoryImpl;
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Str; 

class bTrsPaymentService extends Controller
{
    use AutoNumberTrait;

    private $visit;
    private $bPaymentRepo;

    public function __construct(
        bVisitRepositoryImpl $visit,
        bPaymentRepositoryImpl $bPaymentRepo
        )
    {
        $this->visit = $visit;
        $this->bPaymentRepo = $bPaymentRepo;
    }

    public function createTrs(Request $request)
    {
        // validate 
        $request->validate([
            "NoEpisode" => "required",
            "NoRegistrasi" => "required",
            "Paymentdate" => "required",
            "Jam" => "required",
            "TotalPaid" => "required",
            "Ammount" => "required",
            // "PendapatanPoli" => "required",
            // "PendapatanApotik" => "required",
            // "PendapatanRadiologi" => "required",
            // "PendapatanLab" => "required",
            "Kasir" => "required",
            "Billto" => "required",
            "Descripton" => "required",
            "Id_Kasir" => "required",
        ]);

        //cek statusnya masih open atau sudah close bill
        $getReg = $this->visit->getRegistrationRajalbyNoreg($request->NoRegistrasi);
        
        if ($getReg->first()->StatusID == '4'){
            return $this->sendError('Registrasi Sudah Close !', []);
        }

        //cek sudah ada payment atau belum
        $getPayment = $this->bPaymentRepo->getPaymentByNoReg($request->NoRegistrasi);
        if ($getPayment->count() > 0){
            return $this->sendError('Registrasi Sudah Ada Payment ! Silahkan Dicek Kembali !', []);
        }

        try {   
            //$kdawal = 'KUJ';
            if ($getReg->first()->TipePasien = '1'){
                $kdawal = 'KUJ';
            }else{
                $kdawal = 'PRJ';
            }
            $kodetengah = date('dmy', strtotime($request->Paymentdate));
            $dateconvert = date('Y-m-d', strtotime($request->Paymentdate));
            //getmaxcode
            $getmax = $this->bPaymentRepo->getMaxCode($dateconvert,$kdawal);
            
            if ($getmax->count() > 0) {
                $autonumber =  sprintf("%04s", (int)$getmax->first()->urutkwitansi + 1);
            } else {
                $autonumber = sprintf("%04s", 1);
            }
            $nofinalkwitansi = $kdawal.'-'.$kodetengah.'-'.$autonumber;
            //create
            //create payment header
            $getlastid = $this->bPaymentRepo->createHdr($request,$nofinalkwitansi);
            $request['TipePembayaran'] = 'QRIS';
            //create payment detail
            $this->bPaymentRepo->createDtl($request,$getlastid);
            //close billing
            $this->bPaymentRepo->closeBill($request);
            $this->bPaymentRepo->closeBillDataRWJ($request);
            return $this->sendResponse($getlastid, "Payment Berhasil Dibuat !");
            
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
        
    }

}
