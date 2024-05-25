<?php

namespace App\Http\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\aBookingBedRepositoryImpl;
use App\Traits\AutoNumberTrait;

class aBookingBedService extends Controller
{
    use AutoNumberTrait;

    private $aBookingBedRepositoryImpl;

    public function __construct(aBookingBedRepositoryImpl $aBookingBedRepositoryImpl)
    {
        $this->aBookingBedRepositoryImpl = $aBookingBedRepositoryImpl;
    }

    public function createTrs(Request $request)
    {
        // validate 
        $request->validate([
            "transactiondate" => "required",
            "bookingbeddate" => "required",
            "classid" => "required",
            "roomid" => "required",
            "bedid" => "required",
            "notes" => "required",
            //"medicalrecordnumber" => "required",
            "patientname" => "required",
            "userentri" => "required",
            "jenisbooking" => "required",
        ]);

        try {   
            if ($request->jenispasien == 'Lama'){
                //cek jika ada nomr yang masih ada aktif reservasi di hari yang sama
                $count = $this->aBookingBedRepositoryImpl->getTrsBookingBedByMRActiveSameDay($request)->count();
                if ($count > 0) {
                    return $this->sendError("No MR tersebut ada transaksi booking yang masih aktif di hari yang sama !", []);
                }
            }
            
            //cek jika kamar sudah terisi / booked atau belum
            $count2 = $this->aBookingBedRepositoryImpl->getAvailableBed($request)->count();
            if ($count2 == 0) {
                return $this->sendError("Kamar tersebut tidak tersedia !", []);
            }

             //cek jika tanggal reservasi melebihi tanggal hari ini atau tidak
             $datebooking_int = strtotime($request->bookingbeddate);
             $datenow_int = strtotime(date('Y-m-d'));
             if ($datebooking_int < $datenow_int){
                 return $this->sendError("Tanggal reservasi kurang dari hari ini !", []);
             }


            //update status terpakai ke MstrRoomID
            $this->aBookingBedRepositoryImpl->updateStatusMasterBedTerbooking($request->bedid);
            

            //gen number 
            $getmax = $this->aBookingBedRepositoryImpl->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->transactioncode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->BookingNumber($request, $TransactionCode);
            //create
            $this->aBookingBedRepositoryImpl->createTrs($request,$autonumber);
            return $this->sendResponse($autonumber, "Reservasi berhasil dibuat !");
            
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
        
    }

    public function editTrs(Request $request)
    {
          // validate 
          $request->validate([
            "transactioncode" => "required",
            "transactiondate" => "required",
            "bookingbeddate" => "required",
            "classid" => "required",
            "roomid" => "required",
            "bedid" => "required",
            "notes" => "required",
            //"medicalrecordnumber" => "required",
            "patientname" => "required",
            "userupdate" => "required",
            "jenisbooking" => "required",
        ]);

        try {   
            //cek jika no trs ini masih berlaku atau tidak
            $databook = $this->aBookingBedRepositoryImpl->getTrsBookingBedByTrsCodeActive($request->transactioncode);
            if ($databook->count() == 0) {
                return $this->sendError("Nomor transaksi tidak ditemukan !", []);
            }
            $bedid_old = $databook->first()->bedid;

            if ($bedid_old != $request->bedid){
                //cek jika kamar sudah terisi / booked atau belum
                $count2 = $this->aBookingBedRepositoryImpl->getAvailableBed($request)->count();
                if ($count2 == 0) {
                    return $this->sendError("Kamar tersebut tidak tersedia !", []);
                }
            }

            //cek jika tanggal reservasi melebihi tanggal hari ini atau tidak
            $datebooking_int = strtotime($request->bookingbeddate);
            $datenow_int = strtotime(date('Y-m-d'));
            if ($datebooking_int < $datenow_int){
                return $this->sendError("Tanggal reservasi kurang dari hari ini !", []);
            }

            //update bed lama status tersedia ke MstrRoomID
            $this->aBookingBedRepositoryImpl->updateStatusMasterBedTersedia($bedid_old);

            
            //update bed baru status terpakai ke MstrRoomID
            $this->aBookingBedRepositoryImpl->updateStatusMasterBedTerbooking($request->bedid);

            //update BookingBeds
            $data =$this->aBookingBedRepositoryImpl->editTrs($request);
            return $this->sendResponse([], "Reservasi berhasil diperbarui !");
            
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
        
    }

    public function voidTrs(Request $request)
    {
          // validate 
          $request->validate([
            "transactioncode" => "required",
            "uservoid" => "required",
            "alasanvoid" => "required",
        ]);

        try {   
            //cek jika no trs ini masih berlaku atau tidak
            $databook = $this->aBookingBedRepositoryImpl->getTrsBookingBedByTrsCodeActive($request->transactioncode);
            if ($databook->count() == 0) {
                return $this->sendError("Nomor transaksi tidak ditemukan !", []);
            }
            
             //update bed status tersedia ke MstrRoomID
             $this->aBookingBedRepositoryImpl->updateStatusMasterBedTersedia($databook->first()->bedid);

            //update trs bookingbeds
            $data =$this->aBookingBedRepositoryImpl->voidTrs($request);
            return $this->sendResponse([], "Reservasi berhasil dibatalkan !");
            
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
        
    }
    public function view($id)
    {
        try {   
            $count = $this->aBookingBedRepositoryImpl->getTrsBookingBedByTrsCodeActive($id)->count();
            if ($count > 0) {
                $data = $this->aBookingBedRepositoryImpl->getTrsBookingBedByTrsCodeActive($id)->first();
                return $this->sendResponse($data, "Data booking bed ditemukan.");
            } else {
                return $this->sendError("Data booking bed Not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getListBookingBedActiveByPeriode(Request $request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);

        try {   
            $count = $this->aBookingBedRepositoryImpl->getListBookingBedActiveByPeriode($request)->count();
            if ($count > 0) {
                $data = $this->aBookingBedRepositoryImpl->getListBookingBedActiveByPeriode($request);
                return $this->sendResponse($data, "Data list booking bed ditemukan.");
            } else {
                return $this->sendError("Data list booking bed Not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getListBookingBedArchiveByPeriode(Request $request)
    {
        // validate 
        $request->validate([
            "StartPeriode" => "required",
            "EndPeriode" => "required",
        ]);

        try {   
            $count = $this->aBookingBedRepositoryImpl->getListBookingBedArchiveByPeriode($request)->count();
            if ($count > 0) {
                $data = $this->aBookingBedRepositoryImpl->getListBookingBedArchiveByPeriode($request);
                return $this->sendResponse($data, "Data list booking bed ditemukan.");
            } else {
                return $this->sendError("Data list booking bed Not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function getListBookingBedActiveByNoMR(Request $request)
    {
        // validate 
        $request->validate([
            "medicalrecordnumber" => "required",
        ]);

        try {   
            $count = $this->aBookingBedRepositoryImpl->getListBookingBedActiveByNoMR($request)->count();
            if ($count > 0) {
                $data = $this->aBookingBedRepositoryImpl->getListBookingBedActiveByNoMR($request);
                return $this->sendResponse($data, "Data list booking bed ditemukan.");
            } else {
                return $this->sendError("Data list booking bed Not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }

    public function viewByMatch($id,$nomr)
    {
        try {   
            
            $count = $this->aBookingBedRepositoryImpl->getTrsBookingBedByTrsCodeActive($id)->count();
            if ($count > 0) {
                $data = $this->aBookingBedRepositoryImpl->getTrsBookingBedByTrsCodeActive($id)->first();
                return $this->sendResponse($data, "Data booking bed ditemukan.");
            } else {
                return $this->sendError("Data booking bed Not Found.", []);
            }
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
}
