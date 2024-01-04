<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use App\Http\Repository\aScheduleDoctorRepositoryInterface;
use GuzzleHttp\Psr7\Request;

class bMedicalRecordRepositoryImpl implements bMedicalRecordRepositoryInterface
{
 
    public function create($request, $aktif,$jnsid,$hidden_tptlahir,$NoMrfix,$nourutfixMR,$autonumber)
    {
        return  DB::connection('sqlsrv2')->table("Admision")->insert([
            'ID' => $autonumber,
            'NoMR' => $NoMrfix,
            'PatientName' =>  $request->nama,
            'ID_Card_number' => $request->nik,
            'Tipe_Idcard' => $jnsid,
            'Address' => $request->alamat,
            'Gander' =>  $request->jeniskelamin,
            'BirthPlace' => $hidden_tptlahir,
            'Date_of_birth' =>  $request->tanggallahir,
            'Home Phone' => $request->nohp,
            'Mobile Phone' => $request->nohp,
            'Aktif' => '1' 
        ]);
    } 
    public function patientwlRadiologi($request,$jeniskelamin,$birthday,$patientId,$TRIGGER_DTTM)
    {
        return  DB::connection('sqlsrv8')->table("PATIENTWL")->insert([
            'PATIENT_NAME' => $request->nama,
            'REPLICA_DTTM' => 'ANY',
            'TRIGGER_DTTM' =>  $TRIGGER_DTTM,
            'PATIENT_SEX' => $jeniskelamin,
            'PATIENT_BIRTH_DTTM' => $birthday,
            'ADDRESS' => $request->alamat,
            'PATIENT_ID' =>  $patientId,
            'EVENT_TYPE' => 'ADT^A04' 
        ]);
    }
    public function patientwlLaboratorium($request)
    {
        
    }
    public function createNonWalkin($request, $aktif,$NoMrfix,$nourutfixMR,$autonumber)
    {
        return  DB::connection('sqlsrv2')->table("Admision")->insert([
            'ID' => $autonumber,
            'NoMR' => $NoMrfix,
            'PatientName' =>  $request->nama,
            'ID_Card_number' => $request->nik,
            'Tipe_Idcard' => $request->jenisIdCard,
            'Address' => $request->alamat,
            'Gander' =>  $request->jeniskelamin,
            'BirthPlace' => $request->tempatlahir,
            'Religion' => $request->agama,
            'Marital_status' => $request->statusnikah,
            'Country/Region' => $request->kewarganegaraaan,
            'Marital_status' => $request->statusnikah,
            'Education' => $request->pendidikan,
            'Ocupation' => $request->pekerjaan,
            'E-mail Address' => $request->email,
            'Date_of_birth' =>  $request->tanggallahir,
            'Home Phone' => $request->nohp,
            'Mobile Phone' => $request->nohp,
            'Aktif' => $aktif,
            'State/Province' => $request->provinsi,
            'City' => $request->kabupaten,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'ZIP/Postal Code' => $request->kodepos
        ]);
    } 
    public function createWalkin($request, $aktif,$jnsid,$hidden_tptlahir,$NoMrfix,$nourutfixMR,$autonumber)
    {
        return  DB::connection('sqlsrv2')->table("Admision_walkin")->insert([
            'ID' => $autonumber,
            'NoMR' => $NoMrfix,
            'PatientName' =>  $request->nama,
            'ID_Card_number' => $request->nik,
            'Tipe_Idcard' => $jnsid,
            'Address' => $request->alamat,
            'Gander' =>  $request->jeniskelamin,
            'BirthPlace' => $hidden_tptlahir,
            'Date_of_birth' =>  $request->tanggallahir,
            'Home Phone' => $request->nohp,
            'Mobile Phone' => $request->nohp,
            'Aktif' => '1' 
        ]);
    } 
    public function getMedrecbyNIK($nik)
    {
        return  DB::connection('sqlsrv2')->table("Admision")
        ->select( 'PatientName',  'Date_of_birth','Gander','Marital_status',
        'Address' ,DB::raw('[E-mail Address]  AS Email') , 'ID_Card_number',DB::raw('[Home Phone]  AS tlp'),
        DB::raw('[Mobile Phone]  AS Hp'),
        'NoMR','kelurahan')
        ->where('ID_Card_number', $nik)
        ->where('Aktif', '1')
        ->orderBy('ID', 'desc')
        ->get();
    }
    public function getMedrecbyNoMR($NoMR)
    {
        return  DB::connection('sqlsrv2')->table("Admision")
        ->select( 'PatientName',  'Date_of_birth','Gander','Marital_status',
        'Address' ,DB::raw('[E-mail Address]  AS Email') , 'ID_Card_number',DB::raw('[Home Phone]  AS tlp'),
        DB::raw('[Mobile Phone]  AS Hp'),
        'NoMR')
        ->where('NoMR', $NoMR)
        ->where('Aktif', '1')
        ->orderBy('ID', 'desc')
        ->get();
    }
    public function getMedrecWalkinbyNoMR($NoMR)
    {
        return  DB::connection('sqlsrv2')->table("Admision_walkin")
        ->select( 'PatientName',  'Date_of_birth','Gander','Marital_status',
        'Address' ,DB::raw('[E-mail Address]  AS Email') , 'ID_Card_number',DB::raw('[Home Phone]  AS tlp'),
        DB::raw('[Mobile Phone]  AS Hp'),
        'NoMR')
        ->where('NoMR', $NoMR)
        ->where('Aktif', '1')
        ->orderBy('ID', 'desc')
        ->get();
    }
    public function getMedrecWalkinbyNIK($nik)
    {
        return  DB::connection('sqlsrv2')->table("Admision_walkin")
        ->select( 'PatientName',  'Date_of_birth','Gander','Marital_status',
                    'Address' ,DB::raw('[E-mail Address]  AS Email') , 'ID_Card_number',DB::raw('[Home Phone]  AS tlp'),
                    DB::raw('[Mobile Phone]  AS Hp'),
                    'NoMR')
        ->where('ID_Card_number', $nik)
        ->where('Aktif', '1')
        ->orderBy('ID', 'desc')
        ->get();
    }
    public function getMedrecNumberMax()
    {
        return  DB::connection('sqlsrv2')->table("Admision")->find(DB::connection('sqlsrv2')->table("Admision")->max('ID'));
    }
    public function getMedrecWalkinNumberMax()
    {
        return  DB::connection('sqlsrv2')->table("Admision_walkin")->find(DB::connection('sqlsrv2')->table("Admision_walkin")->max('ID'));
    }
    public function viewbymedrecNonWakinbyDob($request)
    {
        return  DB::connection('sqlsrv2')->table("Admision")
        ->select( 'PatientName',  'Date_of_birth','Gander','Marital_status',
        'Address' ,DB::raw('[E-mail Address]  AS Email') , 'ID_Card_number',DB::raw('[Home Phone]  AS tlp'),
        DB::raw('[Mobile Phone]  AS Hp'),
        'NoMR')
        ->where('NoMR', $request->nomr)
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), Date_of_birth, 111), '/','-')"), $request->tgllahir)
        ->where('Aktif', '1')
        ->orderBy('ID', 'desc')
        ->get();
    }
    public function verifymedrecdobNIk($request)
    {
        return  DB::connection('sqlsrv2')->table("Admision")
        ->select( 'PatientName',  'Date_of_birth','Gander','Marital_status',
        'Address' ,DB::raw('[E-mail Address]  AS Email') , 'ID_Card_number',DB::raw('[Home Phone]  AS tlp'),
        DB::raw('[Mobile Phone]  AS Hp'),
        'NoMR')
        ->where('ID_Card_number', $request->nik)
        ->where(DB::raw("replace(CONVERT(VARCHAR(11), Date_of_birth, 111), '/','-')"), $request->tgllahir)
        ->where('Aktif', '1')
        ->orderBy('ID', 'desc')
        ->get();
    }
    public function updateBookingByIDMobile($request){
        $updatesatuan =  DB::connection('sqlsrv3')->table('Apointment')
        ->where('Userid_Mobile', $request->username) 
            ->update([ 
                'NoMR' => $request->nomr
            ]);
        return $updatesatuan;
    }
   
}
