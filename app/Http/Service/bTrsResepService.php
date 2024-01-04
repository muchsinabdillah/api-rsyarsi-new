<?php 
 
namespace App\Http\Service;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use App\Traits\AutoNumberTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator; 
use App\Http\Repository\aHnaRepositoryImpl;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\aDoctorRepositoryImpl;
use App\Http\Repository\bTarifRepositoryImpl; 
use App\Http\Repository\aTrsResepRepositoryImpl;

class bTrsResepService extends Controller {
    use AutoNumberTrait; 
    private $visitRepository;
    private $trsResep;
    private $doctorRepository;
    private $aHnaRepository; 

    public function __construct( 
        bVisitRepositoryImpl $visitRepository,
        aTrsResepRepositoryImpl $trsResep,
        aDoctorRepositoryImpl $doctorRepository,
        aHnaRepositoryImpl $aHnaRepository
        )
    {
        $this->visitRepository = $visitRepository;   
        $this->trsResep = $trsResep;   
        $this->doctorRepository = $doctorRepository;   
        $this->aHnaRepository = $aHnaRepository;   
    }

    public function viewOrderResepbyTrs(Request $request){
        try {   
            $datadokter = [];
            $viewResepHeader = $this->trsResep->viewResepHeader($request->IdResep)->first();
            $viewResepDetail = $this->trsResep->viewResepDetail($request->IdResep);
            $response = [
                'DataResep' => $viewResepHeader, 
                'listObat' => $viewResepDetail, 
            ];
            return $this->sendResponse($response, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
    public function viewOrderResepDetail(Request $request){
        try {    
            $viewResepDetail = $this->trsResep->viewResepDetail($request->IdResep);
       
            $rows = array();
            foreach ($viewResepDetail as $key2) {
                $hna = $this->aHnaRepository->getHnaHighPeriodik($key2->IdBarang,'2023-11-08');
         
                if($hna->count() < 1 ){
                    $harga = 0;
                }else{
                    $datahna = $hna->first()->first();
                    $hargadasar = $datahna->NominalHna;
                    $hargaprofit = $hargadasar*1.4; 
                    $hargauangr = $hargaprofit*1.1;
                    $harga = $hargauangr+400;
                }
                $pasing['IdBarang'] = $key2->IdBarang;
                $pasing['NamaObat'] = $key2->NamaObat;
                $pasing['Quantity'] = $key2->Quantity;
                $pasing['Signa'] = $key2->Signa; 
                $pasing['Harga'] = $harga;
                $rows[] = $pasing;
            } 
            return $this->sendResponse($rows, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
    }
     public function viewOrderReseV2pbyDatePeriode(Request $request) {
        try { 
            $viewResepDetail = $this->trsResep->viewOrderReseV2pbyDatePeriode($request); 
            return $this->sendResponse($viewResepDetail, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }

     public function viewOrderResepbyOrderIDV2(Request $request) {
         // validate 
         $request->validate([
            "OrderID" => "required"
        ]);

         // cek ada gak datanya
         if ($this->trsResep->viewOrderResepbyOrderIDV2($request->OrderID)->count() < 1) {
            return $this->sendError('Order ID Number Not Found !', []);
        }

        try { 
           
            // Db Transaction
            DB::beginTransaction();
            $viewResepDetail = $this->trsResep->viewOrderResepbyOrderIDV2($request->OrderID); 

           
            
            return $this->sendResponse($viewResepDetail, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }

     public function viewOrderResepDetailbyOrderIDV2(Request $request) {
        try { 
            $viewResepDetail = $this->trsResep->viewOrderResepDetailbyOrderIDV2($request->OrderID);

            $rows = array();
            foreach ($viewResepDetail as $key2) {
                $hna = $this->aHnaRepository->getHnaHighPeriodik($key2->KodeBarang,date('Y-m-d'));
         
                if($hna->count() < 1 ){
                    $harga = 0;
                }else{
                    $datahna = $hna->first()->first();
                    $hargadasar = $datahna->NominalHna;
                    $hargaprofit = $hargadasar*1.4; 
                    $hargauangr = $hargaprofit*1.1;
                    $harga = $hargauangr+400;
                }
                $pasing['Harga'] = round($harga);
                $pasing['ID'] = $key2->ID;
                $pasing['IdOrderResep'] = $key2->IdOrderResep;
                $pasing['KodeBarang'] = $key2->KodeBarang;
                $pasing['NamaBarang'] = $key2->NamaBarang;
                $pasing['QryOrder'] = $key2->QryOrder;
                $pasing['QryRealisasi'] = $key2->QryRealisasi;
                $pasing['Signa'] = $key2->Signa;
                $pasing['SignaTerjemahan'] = $key2->SignaTerjemahan;
                $pasing['Keterangan'] = $key2->Keterangan;
                $pasing['Review'] = $key2->Review;
                $pasing['HasilReview'] = $key2->HasilReview;
                $pasing['Batal'] = $key2->Batal;
                $pasing['TglBatal'] = $key2->TglBatal;
                $pasing['PetugasBatal'] = $key2->PetugasBatal;
                $pasing['Racik'] = $key2->Racik;
                $pasing['Header'] = $key2->Header;
                $pasing['Satuan'] = $key2->Satuan;
                $pasing['Satuan_Beli'] = $key2->Satuan_Beli;
                $pasing['Konversi_satuan'] = $key2->Konversi_satuan;
                $rows[] = $pasing;
            } 
            
            return $this->sendResponse($rows, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }

     public function editSignaTerjemahanbyID(Request $request) {
         // validate 
         $request->validate([
            "ID" => "required",
            "SignaTerjemahan" => "required"
        ]);
        try { 
            // Db Transaction
            DB::beginTransaction();
            $this->trsResep->editSignaTerjemahanbyID($request->ID,$request->SignaTerjemahan);
            DB::commit();
            return $this->sendResponse([], "Update Successfully !");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }

     public function viewprintLabelbyID(Request $request) {
        try { 
            $viewResepDetail = $this->trsResep->viewprintLabelbyID($request->ID); 
            return $this->sendResponse($viewResepDetail, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }

     public function getPrinterLabel(Request $request) {
        try { 
            // cek ada gak datanya
            if ($this->trsResep->getPrinterLabel($request)->count() < 1) {
                return $this->sendError('Printer Not Found !', []);
            }
            $viewResepDetail = $this->trsResep->getPrinterLabel($request); 
            return $this->sendResponse($viewResepDetail, "Data Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }

     public function editReviewbyIDResep(Request $request) {
        // validate 
        $request->validate([
           "IdOrderResep" => "required",
       ]);

        // cek ada gak datanya
        if ($this->trsResep->viewOrderResepbyOrderIDV2($request->IdOrderResep)->count() < 1) {
            return $this->sendError('Order ID Number Not Found !', []);
        }
       try { 
           // Db Transaction
           DB::beginTransaction();
           $this->trsResep->editReviewbyIDResep($request->IdOrderResep);
           DB::commit();
           return $this->sendResponse([], "Update Successfully !");
       }catch (Exception $e) { 
           Log::info($e->getMessage());
           return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
       }
    }
    
}