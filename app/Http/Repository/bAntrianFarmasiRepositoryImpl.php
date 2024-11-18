<?php

namespace App\Http\Repository;
 
use Illuminate\Support\Facades\DB;

class bAntrianFarmasiRepositoryImpl implements bAntrianFarmasiRepositoryInterface
{
    public function CreateAntrian($NoEpisode,$NoRegistrasi,$NoMR,$NoAntrianPoli,
                                    $NoAntrianList,$StatusAntrean,$DateCreated,
                                    $PatientName,$IdUnitFarmasi,
                                    $IDPoliOrder, $NamaPoliOrder, $IDDokter, $NamaDokter,$JenisResep,$NoResep )
    {
        return  DB::connection('sqlsrv')->table("AntrianObatFarmasi")->insert([ 
            'NoEpisode' => $NoEpisode,
            'NoRegistrasi' => $NoRegistrasi,
            'NoResep' => $NoResep,
            'NoMR' => $NoMR,
            'NoAntrianPoli' => $NoAntrianPoli,
            'NoAntrianList' => $NoAntrianList,
            'StatusAntrean' => $StatusAntrean,
            'PatientName' => $PatientName,
            'IDUnitFarmasi' => $IdUnitFarmasi,
            'IDPoliOrder' => $IDPoliOrder,
            'NamaPoliOrder' => $NamaPoliOrder,
            'IDDokter' => $IDDokter,
            'NamaDokter' => $NamaDokter,
            'JenisResep' => $JenisResep,
            'DateCreated' => $DateCreated
        ]);
    }
    public function CreateHistoryAntrian($NoRegistrasi,$StatusAntrean,$DateCreated,$NoResep){
        return  DB::connection('sqlsrv')->table("AntrianObatFarmasiHistory")->insert([ 
            'NoRegistrasi' => $NoRegistrasi,
            'NoResep' => $NoResep,
            'Status' => $StatusAntrean,
            'Waktu' => $DateCreated 
        ]);
    }
    public function getAntrianFarmasibyRegistrasi($request){
        return  DB::connection('sqlsrv')
            ->table("AntrianObatFarmasi")
            ->select(
                'ID' ,'StatusAntrean','DateCreated','DateTaken','DateChecked','DatePacked'
            )
            ->where('NoRegistrasi', $request->NoRegistrasi)
            ->where('Batal', '0')
            ->get();
    }
    public function getAntrianFarmasibyRegistrasiNoresep($request){
        return  DB::connection('sqlsrv')
            ->table("AntrianObatFarmasi")
            ->select(
                'ID' ,'StatusAntrean','DateCreated','DateTaken','DateChecked','DatePacked'
            )
            ->where('NoRegistrasi', $request->NoRegistrasi)
            ->where('NoResep', $request->NoResep)
            
            ->get();
    }
    public function getAntrianFarmasibyRegistrasiCheckin($request){
        return  DB::connection('sqlsrv')
            ->table("AntrianObatFarmasi")
            ->select(
                'ID' 
            )
            ->where('NoRegistrasi', $request->NoRegistrasi)
            ->where('StatusAntrean', '<>',null)
            ->where('DateCreated', '<>',null)
            ->where('IDUnitFarmasi', '<>',null)
            ->where('Batal', '0')
            ->get();
    }
    public function getMaxAntrian($tglbookingfix){
        return  DB::connection('sqlsrv')->table("AntrianObatFarmasi")
        ->select('NoAntrianList')
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), DateCreated, 111), '/','-')"),$tglbookingfix)   
        ->where('Batal', '0')
        ->orderBy('NoAntrianList', 'desc')->first();
    } 
    public function updateStatusProccess($NoRegistrasi,$StatusResep,$DateCreated,$NoResep){
        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoRegistrasi', $NoRegistrasi)
        ->where('Batal', '0')
        ->where('NoResep', $NoResep)
            ->update([
                'StatusAntrean' => $StatusResep,
                'DateProcessed' => $DateCreated
            ]);
        return $updatesatuan;
    }
    public function updateStatusFinish($NoRegistrasi,$StatusResep,$DateCreated,$NoResep){
        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoRegistrasi', $NoRegistrasi)
        ->where('Batal', '0')
        ->where('NoResep', $NoResep)
            ->update([
                'StatusAntrean' => $StatusResep,
                'DateFinished' => $DateCreated
            ]);
        return $updatesatuan;
    }
    public function updateStatusClose($NoRegistrasi,$StatusResep,$DateCreated,$NoResep){
        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoRegistrasi', $NoRegistrasi)
        ->where('Batal', '0')
        ->where('NoResep', $NoResep)
            ->update([
                'StatusAntrean' => $StatusResep,
                'DateClosed' => $DateCreated
            ]);
        return $updatesatuan;
    }
    public function verifiedStatusClose($otp,$DateCreated,$NoResep,$NoRegistrasi){
        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoRegistrasi', $NoRegistrasi)
        ->where('Batal', '0')
        ->where('NoResep', $NoResep)
            ->update([
                'Verified_Closed' => 1,
                'Verified_Number' => $otp,
                'Verified_Date' => $DateCreated
            ]);
        return $updatesatuan;
    }
    public function ListAntrianFarmasi($request)
    {
        return  DB::connection('sqlsrv')->table("AntrianObatFarmasi")
            ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), DateSavedResep, 111), '/','-')"),[$request->StartPeriode, $request->EndPeriode])   
            ->where('IDUnitFarmasi', $request->IdUNitFarmasi)
            ->where('Batal', '0')
            ->whereIn('StatusAntrean', ['CREATED','PROCESSED'])
            ->orderBy('DateCreated', 'ASC')
            ->get();
    }
    public function ListAntrianFarmasiTV($request)
    {
        return  DB::connection('sqlsrv')->table("AntrianObatFarmasi")
            ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), DateCreated, 111), '/','-')"),[$request->StartPeriode, $request->EndPeriode])   
            ->where('IDUnitFarmasi', $request->IdUNitFarmasi)
            ->where('Batal', '0')
            ->whereIn('StatusAntrean', ['CREATED','PROCESSED','FINISHED'])
            ->orderBy('DateCreated', 'ASC')
            ->get();
    }
    public function ListAntrianFinish($request)
    {
        return  DB::connection('sqlsrv')->table("AntrianObatFarmasi")
            ->whereBetween(DB::raw("replace(CONVERT(VARCHAR(11), DateSavedResep, 111), '/','-')"),[$request->StartPeriode, $request->EndPeriode])   
            ->where('IDUnitFarmasi', $request->IdUNitFarmasi)
            ->where('StatusAntrean', 'FINISHED')
            ->where('Batal', '0')
            ->orderBy('DateFinished', 'ASC')
            ->get();
    }
    public function ListHistoryAntrianFarmasi($request)
    {
        return  DB::connection('sqlsrv')->table("AntrianObatFarmasiHistory")
                ->select('Status','Waktu')
                ->where('NoRegistrasi', $request->NoRegistrasi)->orderBy('ID','DESC')
                ->get();
    }
    public function ListDepoFarmasi()
    {
        return  DB::connection('sqlsrv2')
            ->table("MstrUnitPerwatan")
            ->select(
                'ID',
                'NamaUnit'
            )  
             ->where('NamaUnit', 'like', '%farmasi%')->get(); 
    }
    public function UpdateDataVerifikasiAmbilResep($NoResep,$NoRegistrasi,$NoAntrian,$DateCreated,
                                    $UserCreated,$NamaAmbilResep,$NoHandphone,
                                    $Hubungan,$KeteranganLainnya )
    {
        return  DB::connection('sqlsrv')->table("AntrianResepAmbil")->insert([ 
            'NoResep' => $NoResep,
            'NoRegistrasi' => $NoRegistrasi,
            'NoAntrian' => $NoAntrian,
            'DateCreated' => $DateCreated,
            'UserCreated' => $UserCreated,
            'NamaAmbilResep' => $NamaAmbilResep,
            'NoHandphone' => $NoHandphone,
            'Hubungan' => $Hubungan,
            'KeteranganLainnya' => $KeteranganLainnya 
        ]);
    }
    public function getAntrianResepAmbilbyRegistrasi($request){
        return  DB::connection('sqlsrv')
            ->table("AntrianResepAmbil")
            ->select(
                'ID' 
            )
            ->where('NoRegistrasi', $request->NoRegistrasi)
            ->where('NoResep', $request->NoResep)
            
            ->get();
    }
    public function InsertOtpFarmasi($Otp,$NoResep,$Datenow )
    {
        return  DB::connection('sqlsrv2')->table("OTP_VERIFICATION")->insert([ 
            'VERIFICATION_ID' => $NoResep,
            'VERIFICATION_OTP' => $Otp,
            'DATE_CREATED' => $Datenow 
        ]);
    }
    public function UpdateOtpFarmasi($NoResep,$Datenow )
    {
        
        $updatesatuan =  DB::connection('sqlsrv2')->table('OTP_VERIFICATION')
        ->where('VERIFICATION_ID', $NoResep) 
            ->update([
                'EXPIRED' => True,
                'DATE_EXPIRED' => $Datenow 
            ]);
        return $updatesatuan;
    }
    public function UpdateOtpFarmasibyKodeOTP($NoResep,$Datenow ,$otp)
    {
        
        $updatesatuan =  DB::connection('sqlsrv2')->table('OTP_VERIFICATION')
        ->where('VERIFICATION_ID', $NoResep) 
        ->where('VERIFICATION_OTP', $otp) 
            ->update([
                'EXPIRED' => 1,
                'DATE_EXPIRED' => $Datenow 
            ]);
        return $updatesatuan;
    }
    public function getOTPbyCodeOTP($request){
        return  DB::connection('sqlsrv2')
            ->table("OTP_VERIFICATION")
            ->select(
                'ID' 
            )
            ->where('NoRegistrasi', $request->NoRegistrasi)->get();
    }
    public function verifyExpiredOTPbyCodeOTPResep($request){
        return  DB::connection('sqlsrv2')
            ->table("OTP_VERIFICATION")
            ->select(
                'ID' 
            )
            ->where('VERIFICATION_ID', $request->NoResep)
            ->where('VERIFICATION_OTP', $request->OTPNumber)
            ->where('EXPIRED', False)
            ->get();
    }
    public function RuningTextFarmasi($request){
        return  DB::connection('sqlsrv2')
            ->table("RunningTextAntrean")
            ->select(
                'Keterangan' 
            )
            ->where('GroupRuningText', $request->GroupRunningText)->get();
    }
    public function ViewResepMedrecbyDate($request){
        return  DB::connection('sqlsrv')
            ->table("ViewAntrianResepObat")
            ->select(
                 'ID', 'NoAntrianPoli','namaDokter','NamaPoliOrder','StatusAntrean','JenisResep','NamaUnit','NoResep'
            )
            ->where('NoMR', $request->NoMR)
            ->where('Batal', '0')
            ->where('StatusAntrean','<>', 'CLOSED')
            ->where(DB::raw("replace(CONVERT(VARCHAR(11), DateCreated, 111), '/','-')"),$request->Tanggal )   
            ->orderBy('ID', 'DESC')
            ->get();
    }
    public function ViewHistoryResepMedrecbyNoResep($request){
        return  DB::connection('sqlsrv')
            ->table("AntrianObatFarmasiHistory")
            ->select(
                 'ID', 'Status',DB::raw("CONVERT(VARCHAR(11), waktu, 108) as JamHistory"),DB::raw("CONVERT(VARCHAR(10), waktu, 103) as TglHistory")
            )
            ->where('NoResep', $request->NoResep)
             ->orderBy('ID', 'DESC')
            ->get();
    }
    public function UpdateReviewObat($NoResep,$Identitas,$Obat,$Dosis,
                                    $Rute,$Waktu )
    {

        $updatesatuan =  DB::connection('sqlsrv')->table('Orders')
        ->where('Order ID', $NoResep) 
            ->update([
                'RO_Identitas' => $Identitas,
                'RO_Obat' => $Obat,
                'RO_Dosis' => $Dosis,
                'RO_Rute' => $Rute,
                'RO_Waktu' => $Waktu
            ]);
        return $updatesatuan;

    }
    public function getResepObatbyId($request){
        return  DB::connection('sqlsrv')
            ->table("Orders")
            ->select(
                'Order ID' 
            )
            ->where('Order ID', $request->NoResep) 
            ->get();
    }
    public function getResepObatbyNoRegister($request){
        return  DB::connection('sqlsrv')
            ->table("Orders")
            ->select(
                DB::raw("[Order ID] as OrderID"),DB::raw("[Order Date] as OrderDate")
            )
            ->where('NoRegistrasi', $request->NoRegistrasi) 
            ->get();
    }
    public function CreateAntrianNew($NoRegistrasi,$StatusAntrean,$DateCreated,$IDUnitFarmasi)
    {

        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoRegistrasi', $NoRegistrasi) 
            ->update([
                'StatusAntrean' => $StatusAntrean,
                'DateCreated' => $DateCreated,
                'IDUnitFarmasi' => $IDUnitFarmasi 
            ]);
        return $updatesatuan;

    }
    public function CreateAntrianNewSudahReview($NoRegistrasi,$DateCreated,$IDUnitFarmasi,$DateSavedResep)
    {

        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoRegistrasi', $NoRegistrasi) 
            ->update([ 
                'DateCreated' => $DateCreated,
                'IDUnitFarmasi' => $IDUnitFarmasi, 
                'DateSavedResep' => $DateSavedResep 
              
            ]);
        return $updatesatuan;

    }
    public function UpdateDataVerifikasiResepDiAmbil($NoResep,$NoRegistrasi,$DateTaken,$UserTaken )
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoResep', $NoResep) 
        ->where('NoRegistrasi', $NoRegistrasi) 
            ->update([
                'DateTaken' => $DateTaken,
                'UserTaken' => $UserTaken 
            ]);
        return $updatesatuan;
    }
    public function UpdateDataVerifikasiResepDiPeriksa($NoResep,$NoRegistrasi,$DateChecked,$UserChecked )
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoResep', $NoResep) 
        ->where('NoRegistrasi', $NoRegistrasi) 
            ->update([
                'DateChecked' => $DateChecked,
                'UserChecked' => $UserChecked 
            ]);
        return $updatesatuan;
    }
    public function UpdateDataVerifikasiResepDikemas($NoResep,$NoRegistrasi,$DatePacked,$UserPacked )
    {
        $updatesatuan =  DB::connection('sqlsrv')->table('AntrianObatFarmasi')
        ->where('NoResep', $NoResep) 
        ->where('NoRegistrasi', $NoRegistrasi) 
            ->update([
                'DatePacked' => $DatePacked,
                'UserPacked' => $UserPacked 
            ]);
        return $updatesatuan;
    }
    public function getResepObatbyNoRegisterv2($request){
        return  DB::connection('sqlsrv')
            ->table("OrderResep")
            ->select(
                DB::raw("[ID] as OrderID"),DB::raw("[TglResep] as OrderDate")
            )
            ->where('NoRegistrasi', $request->NoRegistrasi) 
            ->get();
    }

    
    public function getResepObatbyIdV2($request){
        return  DB::connection('sqlsrv')
            ->table("OrderResep")
            ->select(
                'ID' 
            )
            ->where('ID', $request->NoResep) 
            ->get();
    }
}
