<?php

namespace App\Http\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
date_default_timezone_set("Asia/Jakarta");
class bEdocumentRepositoryImpl implements bEdocumentRepositoryInterface
{
    public function verify($uuid)
    {
        return  DB::connection('sqlsrv11')->table("TDocumentMasters")   
        ->where('Uuid', $uuid) 
        ->where('Active', '1') 
        ->get();
    }
    public function generalconsen($uuid)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentGeneralConsents")
        ->select(
            'TDocumentGeneralConsents.DocTransactionID as Uuid',
            'TDocumentGeneralConsents.TglCreate as DateCreate', 
            DB::raw(" 'General Consen' as DocumentType"),
            'TDocumentGeneralConsents.Petugas as Petugas',
            'TDocumentGeneralConsents.Divisi as Divisi',
            'TDocumentGeneralConsents.NamaJenisPenanngungJawab as NamaJenisPenanngungJawab',
            'TDocumentGeneralConsents.NamaPenanggungJawab as NamaPenanggungJawab',
            'TDocumentGeneralConsents.AwsUrlDocuments as AwsUrlDocuments',
            DB::raw(" CASE WHEN  TDocumentGeneralConsents.ActiveDocument ='1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END AS StatusDocument ") 
        ) 
        ->where('TDocumentGeneralConsents.DocTransactionID', $uuid)
        ->get();
    }
    public function akadijaroh($uuid)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentIjarohAgreements")
        ->select(
            'TDocumentIjarohAgreements.DocTransactionID as Uuid',
            'TDocumentIjarohAgreements.TglCreate as DateCreate',
            DB::raw(" 'Akad Ijaroh' as DocumentType"), 
            'TDocumentIjarohAgreements.Petugas as Petugas',
            'TDocumentIjarohAgreements.Divisi as Divisi',
            'TDocumentIjarohAgreements.NamaJenisPenanngungJawab as NamaJenisPenanngungJawab',
            'TDocumentIjarohAgreements.NamaPenanggungJawab as NamaPenanggungJawab',
            'TDocumentIjarohAgreements.AwsUrlDocuments as AwsUrlDocuments', 
            DB::raw(" CASE WHEN  TDocumentIjarohAgreements.ActiveDocument ='1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END AS StatusDocument ")
        ) 
        ->where('TDocumentIjarohAgreements.DocTransactionID', $uuid) 
        ->get();
    }
    public function tatatertib($uuid)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentRegulations")
        ->select(
            'TDocumentRegulations.DocTransactionID as Uuid',
            'TDocumentRegulations.TglCreate as DateCreate', 
            DB::raw(" 'Tata Tertib' as DocumentType"), 
            DB::raw(" 'Wali Pasien' as NamaJenisPenanngungJawab"),  
            'TDocumentRegulations.NamaWaliPasien as NamaPenanggungJawab',
            'TDocumentRegulations.AwsUrlDocuments as AwsUrlDocuments', 
            DB::raw(" CASE WHEN  TDocumentRegulations.ActiveDocument ='1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END AS StatusDocument ")
        ) 
        ->where('TDocumentRegulations.DocTransactionID', $uuid) 
        ->get();
    }
    public function hakdankewajiban($uuid)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentRightsandObligations")
        ->select(
            'TDocumentRightsandObligations.DocTransactionID as Uuid',
            'TDocumentRightsandObligations.TglCreate as DateCreate', 
            DB::raw(" 'Hak dan Kwajiban Pasien' as DocumentType"),  
            DB::raw(" 'Wali Pasien' as NamaJenisPenanngungJawab"),  
            'TDocumentRightsandObligations.NamaWaliPasien as NamaPenanggungJawab',
            'TDocumentRightsandObligations.AwsUrlDocuments as AwsUrlDocuments', 
            DB::raw(" CASE WHEN  TDocumentRightsandObligations.ActiveDocument ='1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END AS StatusDocument ")
        ) 
        ->where('TDocumentRightsandObligations.DocTransactionID', $uuid) 
        ->get();
    }
    public function perkiraanbiayaoperasi($uuid)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentEsitimatedOperationgCosts")
        ->select(
            'TDocumentEsitimatedOperationgCosts.DocTransactionID as Uuid',
            'TDocumentEsitimatedOperationgCosts.TglCreate as DateCreate', 
            DB::raw(" 'General Consen' as DocumentType"),
            'TDocumentEsitimatedOperationgCosts.Petugas as Petugas',
            'TDocumentEsitimatedOperationgCosts.Divisi as Divisi',
            DB::raw(" 'Wali/Pasien' as NamaJenisPenanngungJawab"), 
            'TDocumentEsitimatedOperationgCosts.NamaWaliPasien as NamaPenanggungJawab',
            'TDocumentEsitimatedOperationgCosts.AwsUrlDocuments as AwsUrlDocuments',
            DB::raw(" CASE WHEN  TDocumentEsitimatedOperationgCosts.ActiveDocument ='1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END AS StatusDocument ") 
        ) 
        ->where('TDocumentEsitimatedOperationgCosts.DocTransactionID', $uuid)
        ->get();
    }
    public function perkiraanbiayanonoperasi($uuid)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentEsitimatedNonOperationgCosts")
        ->select(
            'TDocumentEsitimatedNonOperationgCosts.DocTransactionID as Uuid',
            'TDocumentEsitimatedNonOperationgCosts.TglCreate as DateCreate', 
            DB::raw(" 'General Consen' as DocumentType"),
            'TDocumentEsitimatedNonOperationgCosts.Petugas as Petugas',
            'TDocumentEsitimatedNonOperationgCosts.Divisi as Divisi',
            DB::raw(" 'Wali/Pasien' as NamaJenisPenanngungJawab"), 
            'TDocumentEsitimatedNonOperationgCosts.NamaWaliPasien as NamaPenanggungJawab',
            'TDocumentEsitimatedNonOperationgCosts.AwsUrlDocuments as AwsUrlDocuments',
            DB::raw(" CASE WHEN  TDocumentEsitimatedNonOperationgCosts.ActiveDocument ='1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END AS StatusDocument ") 
        ) 
        ->where('TDocumentEsitimatedNonOperationgCosts.DocTransactionID', $uuid)
        ->get();
    }

    public function getlaboratoriumdocregistrasi($NoRegistrasi)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentLaboratoriumResults")
        ->select(
            'ID',
            'DocTransactionID', 
            'NoTrs_Reff', 
            'NoRegistrasi', 
            'AwsUrlDocuments', 
            'TglCreate', 
            'UserCreate', 
            'ActiveDocument'
        ) 
        ->where('NoRegistrasi', $NoRegistrasi)
        ->get();
    }
    public function getRadiologidocregistrasi($NoRegistrasi)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentRadiologiResults")
        ->select(
            'ID',
            'DocTransactionID', 
            'NoTrs_Reff', 
            'NoRegistrasi', 
            'AwsUrlDocuments', 
            'TglCreate', 
            'UserCreate', 
            'ActiveDocument'
        ) 
        ->where('NoRegistrasi', $NoRegistrasi)
        ->get();
    }
    public function getResumeMedisdocregistrasi($NoRegistrasi)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentMedicalSummaries")
        ->select(
            'ID',
            'DocTransactionID', 
            'NoTrs_Reff', 
            'NoRegistrasi', 
            'AwsUrlDocuments', 
            'TglCreate', 
            'UserCreate', 
            'ActiveDocument'
        ) 
        ->where('NoRegistrasi', $NoRegistrasi)
        ->get();
    }
    public function getResumeMedisbyId($idTrs)
    {
        return  DB::connection('sqlsrv5')
        ->table("MR_Resume_Medis") 
        ->where('ID', $idTrs)
        ->where('statusResume','<>','VERIFIED')
        ->get();
    }
    public function insertOTP($request)
    {
        return  DB::connection('Syslog')->table("Otps")->insert([
            'JENIS_OTP' => $request->jenisotp,  
            'MOBILE_PHONE' => $request->nohp,  
            'KODE_OTP' => $request->otp,   
            'EXPIRED' => $request->isexpired,
            'DATE_CREATE' => Carbon::now()
        ]);
    }
    public function getOTPActive($request)
    {
        return  DB::connection('Syslog')
        ->table("Otps") 
        ->where('KODE_OTP', $request->otp)
        ->where('JENIS_OTP', $request->jenisotp)
        ->where('MOBILE_PHONE', $request->nohp)
        ->where('EXPIRED', '0')
        ->get();
    }
    public function updateOTPExpired($request)
    {
        $updatesatuan =  DB::connection('Syslog')->table('Otps')
        ->where('JENIS_OTP', $request->jenisotp)
        ->where('MOBILE_PHONE', $request->nohp)
        ->where('EXPIRED', '0')
            ->update([
                'EXPIRED' => '1', 
                'DATE_VERIFY' => Carbon::now(), 
                'VERIFY_STATUS' => 'CANCELLED'
            ]);
        return $updatesatuan;
    }
    public function updateOTPVerify($request)
    {
        $updatesatuan =  DB::connection('Syslog')->table('Otps')
        ->where('JENIS_OTP', $request->jenisotp)
        ->where('MOBILE_PHONE', $request->nohp)
        ->where('KODE_OTP', $request->otp)
            ->update([
                'EXPIRED' => '1', 
                'DATE_VERIFY' => Carbon::now(), 
                'VERIFY_STATUS' => 'VERIFIED'
            ]);
        return $updatesatuan;
    }
    public function updateOTPVerifiedResumeMedis($request)
    {
        $updatesatuan =  DB::connection('sqlsrv5')->table('MR_Resume_Medis')
        ->where('ID', $request->idresumemedis) 
            ->update([
                'statusResume' => 'VERIFIED', 
                'DateVerified' => Carbon::now() 
            ]);
        return $updatesatuan;
    }
    //Tambahan 25-12-2023
    public function getPersetujuanTindakandocregistrasi($NoRegistrasi)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentMedicalSummaries")
        ->select(
            'ID',
            'DocTransactionID', 
            'NoTrs_Reff', 
            'NoRegistrasi', 
            'AwsUrlDocuments', 
            'TglCreate', 
            'UserCreate', 
            'ActiveDocument'
        ) 
        ->where('NoRegistrasi', $NoRegistrasi)
        ->get();
    }
    public function getPersetujuanTindakanbyId($idTrs)
    {
        return  DB::connection('sqlsrv5')
        ->table("MR_persetujuan_tindakan") 
        ->where('ID', $idTrs)
        ->where('statusResume','<>','VERIFIED')
        ->get();
    }
    public function getSuketSakitdocregistrasi($NoRegistrasi)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentMedicalSummaries")
        ->select(
            'ID',
            'DocTransactionID', 
            'NoTrs_Reff', 
            'NoRegistrasi', 
            'AwsUrlDocuments', 
            'TglCreate', 
            'UserCreate', 
            'ActiveDocument'
        ) 
        ->where('NoRegistrasi', $NoRegistrasi)
        ->get();
    }
    public function getSuketSakitbyId($idTrs)
    {
        return  DB::connection('sqlsrv5')
        ->table("MR_SuratSakit") 
        ->where('ID', $idTrs)
        ->where('statusResume','<>','VERIFIED')
        ->get();
    }
    public function getSuketSehatdocregistrasi($NoRegistrasi)
    {
        return  DB::connection('sqlsrv11')
        ->table("TDocumentMedicalSummaries")
        ->select(
            'ID',
            'DocTransactionID', 
            'NoTrs_Reff', 
            'NoRegistrasi', 
            'AwsUrlDocuments', 
            'TglCreate', 
            'UserCreate', 
            'ActiveDocument'
        ) 
        ->where('NoRegistrasi', $NoRegistrasi)
        ->get();
    }
    public function getSuketSehatbyId($idTrs)
    {
        return  DB::connection('sqlsrv5')
        ->table("MR_SuratKetSehat") 
        ->where('ID', $idTrs)
        ->where('statusResume','<>','VERIFIED')
        ->get();
    }
    public function updateOTPVerifiedPersetujuanTindakan($request)
    {
        $updatesatuan =  DB::connection('sqlsrv5')->table('MR_persetujuan_tindakan')
        ->where('ID', $request->ID) 
            ->update([
                'statusResume' => 'VERIFIED', 
                'DateVerified' => Carbon::now() 
            ]);
        return $updatesatuan;
    }
    public function updateOTPVerifiedSuketSakit($request)
    {
        $updatesatuan =  DB::connection('sqlsrv5')->table('MR_SuratSakit')
        ->where('ID', $request->ID) 
            ->update([
                'statusResume' => 'VERIFIED', 
                'DateVerified' => Carbon::now() 
            ]);
        return $updatesatuan;
        
    }
    public function updateOTPVerifiedSuketSehat($request)
    {
        $updatesatuan =  DB::connection('sqlsrv5')->table('MR_SuratKetSehat')
        ->where('ID', $request->ID) 
            ->update([
                'statusResume' => 'VERIFIED', 
                'DateVerified' => Carbon::now() 
            ]);
        return $updatesatuan;
    }
}