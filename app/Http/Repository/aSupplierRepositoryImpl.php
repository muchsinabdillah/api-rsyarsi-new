<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class aSupplierRepositoryImpl implements aSupplierRepositoryInterface
{
    public function addSupplier($request)
    {

 
        return  DB::connection('sqlsrv')->table("Suppliers")->insert([
            'IdPabrikan' => $request->IdPabrikan,
            'NamaBank' => $request->NamaBank,
            'NoRekening' => $request->NoRekening,
            'Company' => $request->Company,
            'Last Name' => $request->last_name,
            'First Name' => $request->first_name,
            'E-mail Address' => $request->Email_Address,
            'Home Phone' => $request->home_phone,
            'Mobile Phone' => $request->mobile_phone,
            'Fax Number' => $request->fax_number,
            'Address' => $request->Address,
            'City' => $request->City,
            'State/Province' => $request->Province,
            'ZIP/Postal Code' => $request->ZIP,
            'Country/Region' => $request->Country,
            'Notes' => $request->Notes,
            'lock' => $request->lock,
            'suplier' => $request->suplier,
            'LeadTime_Days' => $request->LeadTime,
            'LamaJatuhTempo_Days' => $request->JatuhTempo,
        ]);
    }
    public function editSupplier($request)
    {

        $updateSupplier =  DB::connection('sqlsrv')->table('Suppliers')
            ->where('ID', $request->ID)
            ->update([
            'IdPabrikan' => $request->IdPabrikan,
            'NamaBank' => $request->NamaBank,
            'NoRekening' => $request->NoRekening,
            'Company' => $request->Company,
            'Last Name' => $request->last_name,
            'First Name' => $request->first_name,
            'E-mail Address' => $request->Email_Address,
            'Home Phone' => $request->home_phone,
            'Mobile Phone' => $request->mobile_phone,
            'Fax Number' => $request->fax_number,
            'Address' => $request->Address,
            'City' => $request->City,
            'State/Province' => $request->Province,
            'ZIP/Postal Code' => $request->ZIP,
            'Country/Region' => $request->Country,
            'Notes' => $request->Notes,
            'lock' => $request->lock,
            'suplier' => $request->suplier,
            'LeadTime_Days' => $request->LeadTime,
            'LamaJatuhTempo_Days' => $request->JatuhTempo,
        ]);
        return $updateSupplier;
    }

    public function getSupplierbyId($id)
    {
        return  DB::connection('sqlsrv')
        ->table("Suppliers")
        ->select(
            'ID',
            'IdPabrikan',
            'Company',
            'Last Name as LastName',
            'First Name as FirstName',
            'E-mail Address as Email',
            'Home Phone as HomePhone',
            'Mobile Phone as MobilePhone',
            'Fax Number as FaxNumber',
            'Address',
            'City',
            'State/Province as Province',
            'ZIP/Postal Code as PostalCode',
            'Country/Region as Region ',
            'Notes',
            'lock',
            'suplier',
            'LeadTime_Days',
            'LamaJatuhTempo_Days',
            'NamaBank',
            'NoRekening'
        )
        ->where('ID', $id)->get();
    }
    public function getSupplierAll()
    {
        return  DB::connection('sqlsrv')
        ->table("Suppliers")
        ->select(
            'ID',
            'IdPabrikan',
            'Company',
            'Last Name as LastName',
            'First Name as FirstName',
            'E-mail Address as Email',
            'Home Phone as HomePhone',
            'Mobile Phone as MobilePhone',
            'Fax Number as FaxNumber',
            'Address',
            'City',
            'State/Province as Province',
            'ZIP/Postal Code as PostalCode',
            'Country/Region as Region ',
            'Notes',
            'lock',
            'suplier',
            'LeadTime_Days',
            'LamaJatuhTempo_Days',
            )
        ->get();
    }
}
