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
use App\Traits\HargaJualTrait;
use App\Http\Repository\aMasterUnitRepositoryImpl;
use App\Http\Repository\aSalesRepositoryImpl;

class bTrsResepService extends Controller {
    use AutoNumberTrait; 
    use HargaJualTrait;
    private $visitRepository;
    private $trsResep;
    private $doctorRepository;
    private $aHnaRepository; 
    private $unitRepository;
    private $aSalesRepository; 

    public function __construct( 
        bVisitRepositoryImpl $visitRepository,
        aTrsResepRepositoryImpl $trsResep,
        aDoctorRepositoryImpl $doctorRepository,
        aHnaRepositoryImpl $aHnaRepository,
        aMasterUnitRepositoryImpl $unitRepository,
        aSalesRepositoryImpl $aSalesRepository
        )
    {
        $this->visitRepository = $visitRepository;   
        $this->trsResep = $trsResep;   
        $this->doctorRepository = $doctorRepository;   
        $this->aHnaRepository = $aHnaRepository;   
        $this->unitRepository = $unitRepository;
        $this->aSalesRepository = $aSalesRepository;
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
        $viewResepDetail = $this->trsResep->viewOrderResepDetailbyOrderIDV2($request->OrderID);
        foreach ($viewResepDetail as $key2) {
            if ($this->aHnaRepository->getHnaHighPeriodik($key2->KodeBarang,date('Y-m-d'))->count() < 1 && $key2->KodeBarang != null) {
                return $this->sendError('Kode barang '.$key2->KodeBarang.' tidak ada di hna !', []);
            }
        } 

        try { 
            //$viewResepDetail = $this->trsResep->viewOrderResepDetailbyOrderIDV2($request->OrderID);

            $rows = array();
            foreach ($viewResepDetail as $key2) {
                $hna = $this->aHnaRepository->getHnaHighPeriodik($key2->KodeBarang,date('Y-m-d'));
         
                if($hna->count() < 1 ){
                    $harga = 0;
                }else{
                    $datahna = $hna->first()->first();
                    $harga = $this->HargaJual($request->GroupJaminan,$request->NoRegistrasi,$datahna->NominalHna,$key2->Category,$request->Kelas,$key2->Konversi_satuan);
                }
                $hnatax = ($datahna->NominalHna*11)/100;
                $hnataxfix = $datahna->NominalHna+$hnatax;
                $pasing['Harga'] = round($harga);
                $pasing['UangR'] = 400;
                $pasing['Embalase'] = 400;
                $pasing['HnaReal'] = $datahna->NominalHna;
                $pasing['TaxReal'] = $hnataxfix;
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

    public function addTebusResep(Request $request){
        
        
        try{
            DB::connection('sqlsrv3')->beginTransaction();
            $datenow = Carbon::now()->toDateString();
            $timenow = Carbon::now()->toTimeString();
            
            // validasi 
            if ($request->NoMR == "") {  
                return $this->sendError("No. Medical Record Kosong.", []);
            }
            if ($request->KodePoli == "") {  
                return $this->sendError("Kode Poliklinik Kosong.", []);
            }
            if ($request->KodeDokter == "") {  
                return $this->sendError("Kode Dokter Kosong.", []);
            }
            if ($request->IdAdmin == "") {    
                return $this->sendError("Kode Administrasi Kosong.", []);
            }
            if ($request->IdCaraMasuk == "") {  
                return $this->sendError("ID Cara Masuk Kosong.", []);
            }
            if ($request->TipeRegistrasi == "") {  
                return $this->sendError("Tipe Registrasi Kosong.", []);
            }
            if ($request->TglRegistrasi == "") {  
                return $this->sendError("Tanggal Kosong.", []);
            }
            if ($request->Company == "") {  
                return $this->sendError("Company Kosong.", []);
            }
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request->TglRegistrasi)) {
                $metadata = array(
                    'message' => "Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd", // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorNew($metadata,null);
            }
            if (date("Y-m-d",strtotime($request->TglRegistrasi)) <  date("Y-m-d",strtotime($datenow))) {
                $metadata = array(
                    'message' => 'Tanggal Periksa Tidak Berlaku.', // Set array status dengan success     
                    'code' => 201, // Set array nama dengan isi kolom nama pada tabel siswa 
                );
                return  $this->sendErrorNew($metadata,null);
            }
            if($request->Group_Transaksi == "RESEP"){
                if ($this->trsResep->viewOrderResepbyOrderIDV2($request->NoResep)->first()->Iter < $this->aSalesRepository->getSalesbyNoResep($request)->count()){
                    return $this->sendError('Resep sudah pernah dibuat dan berada batas iter resep ! Cek di [List Penjualan] !', []);
                }
            }

                    
                    $NoMrConvert = str_replace("-", "", $request->NoMR);
                    //get max visit
                    $maxVisit = $this->visitRepository->getMaxnumberVisit();
                    $maxVisit->ID++;
                    
                    $operator = "2852"; 
                    $CaraBayar = $request->CaraBayar;
                    $idCaraMasuk = $request->IdCaraMasuk;
                    $idAdmin = $request->IdAdmin;
                    $TelemedicineIs="0";
                    $Tipe_Registrasi = $request->TipeRegistrasi;

                    $datenow2 = date('Y-m-d', strtotime($request->TglRegistrasi));
                    $datenowcreate = $datenow2;
                    $datenow = date('dmy', strtotime($request->TglRegistrasi));

                    if($CaraBayar == "1"){ 
                        $kodeRegAwalXX = "RJUM";
                    }elseif($CaraBayar == "2"){ 
                        $kodeRegAwalXX = "RJAS";
                    }elseif($CaraBayar == "5"){ 
                        $kodeRegAwalXX = "RJJP";
                    }
                    if($request->TipeRegistrasi == "1"){ // bpjs
                        $Perusahaan = "313";
                    }else{
                        if($CaraBayar == "1"){
                            $Perusahaan = "315";
                        }else{
                            $Perusahaan = $request->Idjaminan;
                        }
                    }
                    $jamPraktek = "08:00-17:00";
                    if($request->JamPraktek == ""){
                        $jamPraktek = "08:00-17:00";
                    }else{
                        $jamPraktek = $request->JamPraktek;
                    }

                    $namaPaketMCU = $request->NamaPaketMCU;
                    if($namaPaketMCU == ""){
                        $Catatan = "";
                    }else{
                        $Catatan = $request->NamaPaketMCU;
                    }

                    $dataDoctorbpjs = $this->doctorRepository->getDoctorbyId($request->KodeDokter);
                    if ( $dataDoctorbpjs->count() < 1 ) {
                        return  $this->sendError('Data ID Dokter Tidak ditemukan.',[]);
                    }else{
                        $dtdr = $dataDoctorbpjs->first(); 
                        $IdDokter = $dtdr->ID;
                        $CodeAntrian = $dtdr->CodeAntrian;
                        $NamaDokter = $dtdr->NamaDokter; 
                        $ID_Dokter_BPJS = $dtdr->ID_Dokter_BPJS;  
                        $NAMA_Dokter_BPJS = $dtdr->NAMA_Dokter_BPJS; 
                        if($request->TipeRegistrasi == "1"){
                            // if($ID_Dokter_BPJS == null || $ID_Dokter_BPJS == ""){
                            //     return  $this->sendError('Data ID Dokter BPJS Belum di Maping dalam SIMRS.',[]);
                            // }
                        }
                    }

                    //2. Validasi Unit
                    $dataunitbpjs = $this->unitRepository->getUnitById($request->KodePoli);
                    if ( $dataunitbpjs->count() < 1 ) {
                        return  $this->sendError('Data ID Poliklinik Tidak ditemukan.',[]);
                    }else{
                        $dtdr = $dataunitbpjs->first();
                        $IdGrupPerawatan = $dtdr->ID;
                        $NamaGrupPerawatan = $dtdr->NamaUnit; 
                        $CodeSubBPJS = $dtdr->CodeSubBPJS;  
                        if($request->TipeRegistrasi == "1"){
                            // if($CodeSubBPJS == null || $CodeSubBPJS == ""){
                            //     return  $this->sendError('Data ID Poliklinik BPJS Belum di Maping dalam SIMRS.',[]);
                            // }
                        }
                    } 

                    

            
            $getiter = $this->trsResep->viewOrderResepbyOrderIDV2($request->NoResep)->first()->IterRealisasi;
            if ($getiter == null){
                $getiter = 0;
            }
            $getiter += 1;
            $this->trsResep->updateIterReal($request->NoResep,$getiter);
                
            // INSERT REGISTRATION
            $FisioterapiFlag = '0';
            if($IdGrupPerawatan == "17"){
                $FisioterapiFlag = '1';
            }else{
                $FisioterapiFlag = '0';
            }

            $NoregistrationRajal = $this->genNumberRegistrationRajal($datenowcreate,$kodeRegAwalXX,$datenow,$request->NoMR);
            $NoEpisode = $NoregistrationRajal[3];
            $auto_eps = $NoregistrationRajal[4];
            $id_eps = $NoregistrationRajal[5];
            $nofixReg = $NoregistrationRajal[1]; 

            $idno_urutantrian = '';
            $NoAntrianAll = '';
            $NamaSesion = '';
            $ID_JadwalPraktek = '';
           
            if($CaraBayar == "2"){
                $this->visitRepository->addRegistrationRajalAsuransi($maxVisit->ID,$NoEpisode,$nofixReg,$NamaGrupPerawatan,$request->NoMR,
                $request->CaraBayar,$IdGrupPerawatan,$IdDokter,$idno_urutantrian,$NoAntrianAll,
                $request->Company,$NamaSesion,$TelemedicineIs,$request->TglRegistrasi.' '.$timenow,
                $request->TglRegistrasi.' '.$timenow,$operator,$CaraBayar,$Perusahaan,$idCaraMasuk,
                $idAdmin,$Tipe_Registrasi,$ID_JadwalPraktek,$Catatan,$FisioterapiFlag);
            }else{
                $this->visitRepository->addRegistrationRajal($maxVisit->ID,$NoEpisode,$nofixReg,$NamaGrupPerawatan,$request->NoMR,
                $request->CaraBayar,$IdGrupPerawatan,$IdDokter,$idno_urutantrian,$NoAntrianAll,
                $request->Company,$NamaSesion,$TelemedicineIs,$request->TglRegistrasi.' '.$timenow,
                $request->TglRegistrasi.' '.$timenow,$operator,$CaraBayar,$Perusahaan,$idCaraMasuk,
                $idAdmin,$Tipe_Registrasi,$ID_JadwalPraktek,$Catatan,$FisioterapiFlag);
            }

            $getmax = $this->aSalesRepository->getMaxCode($request);
            if ($getmax->count() > 0) {
                foreach ($getmax as $datanumber) {
                    $TransactionCode = $datanumber->TransactionCode;
                }
            } else {
                $TransactionCode = 0;
            }
            $autonumber = $this->SalesResepNumber($request, $TransactionCode);
            
            $request['NoRegistrasi'] = $nofixReg;
            $this->aSalesRepository->addSalesHeader($request, $autonumber);
            //DB::commit();
            
            $response = array(
                'NoEpisode' => $NoEpisode, // Set array status dengan success     
                'NoRegistrasi' => $nofixReg, // Set array status dengan success     
                'NamaGrupPerawatan' => $NamaGrupPerawatan, // Set array status dengan success     
                'NOMR' => $request->NoMR, // Set array status dengan success     
                'Antrian' => $idno_urutantrian, // Set array status dengan success     
                'NoAntrianAll' =>  $NoAntrianAll, // Set array status dengan success   
                'TglRegistrasi' => $request->TglRegistrasi, // Set array status dengan success  
                'JamRegistrasi' =>  $timenow ,// Set array status dengan success  
                'TransactionCode' =>  $autonumber // Set array status dengan success  
            );

            DB::connection('sqlsrv3')->commit();
            return $this->sendResponse($response ,"Registrasi dan transaksi berhasil dibuat!");  


        }catch (Exception $e) { 
            DB::connection('sqlsrv3')->rollBack();
            Log::info($e->getMessage()); 
            return $this->sendError($e->getMessage(), []); 
        }  
    }

    public function viewOrderResepbyDatePeriodeTebus(Request $request) {
        try { 
            $viewResepDetail = $this->trsResep->viewOrderResepbyDatePeriodeTebus($request); 
            return $this->sendResponse($viewResepDetail, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }

     public function viewOrderResepbyDatePeriodeRajal(Request $request) {
        try { 
            $viewResepDetail = $this->trsResep->viewOrderResepbyDatePeriodeRajal($request); 
            return $this->sendResponse($viewResepDetail, "Data Resep Ditemukan.");
        }catch (Exception $e) { 
            Log::info($e->getMessage());
            return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
        }
     }
    
   public function viewOrderResepbyDatePeriodeRanap(Request $request) {
       try { 
           $viewResepDetail = $this->trsResep->viewOrderResepbyDatePeriodeRanap($request); 
           return $this->sendResponse($viewResepDetail, "Data Resep Ditemukan.");
       }catch (Exception $e) { 
           Log::info($e->getMessage());
           return $this->sendError('Data Gagal Di Tampilkan !', $e->getMessage());
       }
    }
    
}