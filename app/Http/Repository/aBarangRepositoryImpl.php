<?php

namespace App\Http\Repository;

use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class aBarangRepositoryImpl implements aBarangRepositoryInterface
{
    public function addBarang($request)
    {
        return  DB::connection('sqlsrv')->table("Products")->insert([
            'Product Code' => $request->ProductCode,
            'Product Name' => $request->ProductName,
            'NamaKMG' => $request->ProductNameAlias,
            'Discontinued' => $request->Discontinue,
            'Category' => $request->Category,
            'Group_DK' => $request->Group_DK,
            'Satuan_Beli' => $request->Satuan_Beli,
            'Unit Satuan' => $request->Unit_Satuan,
            'Konversi_satuan' => $request->Konversi_satuan,
            'Reorder Level' => $request->Reorder_Level,
            'Signa' => $request->Signa,
            'Description' => $request->Description,
            'Composisi' => $request->Composisi,
            'Indikasi' => $request->Indikasi,
            'Dosis' => $request->Dosis,
            'Kontra_indikasi' => $request->Kontra_indikasi,
            'Efek_Samping' => $request->Efek_Samping,
            'Peringatan' => $request->Peringatan,
            'Kemasan' => $request->Kemasan,
            'Kode_Barcode' => $request->Kode_Barcode,
            'flag_telemedicine' => $request->flag_telemedicine,
            'KD_PDP' => $request->KD_PDP,
        ]);
    } 
    public function editBarang($request)
    {

        $updateBarang =  DB::connection('sqlsrv')->table('Products')
            ->where('ID', $request->ID)
            ->update(['Product Code' => $request->ProductCode,
            'Product Name' => $request->ProductName,
            'NamaKMG' => $request->ProductNameAlias,
            'Discontinued' => $request->Discontinue,
            'Category' => $request->Category,
            'Group_DK' => $request->Group_DK,
            'Satuan_Beli' => $request->Satuan_Beli,
            'Unit Satuan' => $request->Unit_Satuan,
            'Konversi_satuan' => $request->Konversi_satuan,
            'Reorder Level' => $request->Reorder_Level,
            'Signa' => $request->Signa,
            'Description' => $request->Description,
            'Composisi' => $request->Composisi,
            'Indikasi' => $request->Indikasi,
            'Dosis' => $request->Dosis,
            'Kontra_indikasi' => $request->Kontra_indikasi,
            'Efek_Samping' => $request->Efek_Samping,
            'Peringatan' => $request->Peringatan,
            'Kemasan' => $request->Kemasan,
            'Kode_Barcode' => $request->Kode_Barcode,
            'flag_telemedicine' => $request->flag_telemedicine,
            'KD_PDP' => $request->KD_PDP
            ]);
        return $updateBarang;
    }
    public function getBarangbyId($id)
    {
        return  DB::connection('sqlsrv')
            ->table("Products")
            ->select(
            'ID',
            'Product Code',
            'Product Name',
            'NamaKMG',
            'Discontinued',
            'Category',
            'Group_DK',
            'Satuan_Beli',
            'Unit Satuan',
            'Konversi_satuan',
            'Reorder Level',
            'Signa',
            'Description',
            'Composisi',
            'Indikasi',
            'Dosis',
            'Kontra_indikasi',
            'Efek_Samping',
            'Peringatan',
            'Kemasan',
            'Kode_Barcode' 
            )
            ->where('ID', $id)
            ->get();
    }
    public function getBarangbyIdAndIDSupplier($request)
    {
        return  DB::connection('sqlsrv')
            ->table("Products_2")
            ->select(
            'IDBarang',
            'IDSupplier' 
            )
            ->where('IDBarang', $request->IDBarang)
            ->where('IDSupplier', $request->IDSupplier)
            ->get();
    }
    public function getBarangAll()
    {
        return  DB::connection('sqlsrv')
            ->table("Products")
            ->select(
            'ID',
            'Product Code',
            'Product Name',
            'NamaKMG',
            'Discontinued',
            'Category',
            'Group_DK',
            'Satuan_Beli',
            'Unit Satuan',
            'Konversi_satuan',
            'Reorder Level',
            'Signa',
            'Description',
            'Composisi',
            'Indikasi',
            'Dosis',
            'Kontra_indikasi',
            'Efek_Samping',
            'Peringatan',
            'Kemasan',
            'Kode_Barcode' 
            )
            ->get();
    }
    // Barang Supplier
    public function addBarangSupplier($request)
    {
        return  DB::connection('sqlsrv')->table("Products_2")->insert([
            'IDBarang' => $request->IDBarang,
            'IDSupplier' => $request->IDSupplier
        ]);
    }
    public function deleteBarangSupplier($request)
    {
        return  DB::connection('sqlsrv')->table("Products_2")
        ->where('IDBarang',$request->IDBarang)
        ->where('IDSupplier', $request->IDSupplier)
        ->delete(); 
    }
    public function getBarangbySuppliers($id)
    {
        return  DB::connection('sqlsrv')
        ->table("Products_2")
        ->select(
            'Products_2.IDBarang as IDBarang',
            'Products_2.IDSupplier as IDSupplier',
            'Suppliers.Company as NamaSupplier'
        )
        ->join('Suppliers', 'Suppliers.ID', '=', 'Products_2.IDSupplier')
        ->where('Products_2.IDBarang', $id)->get();
    }
    // Barang Formularium
    public function addBarangFormularium($request)
    {
        return  DB::connection('sqlsrv')->table("Product_4")->insert([
            'IDBarang' => $request->IDBarang,
            'IDFormularium' => $request->IDFormularium
        ]);
    }
    public function getBarangbyIdAndIDFormularium($request)
    {
        return  DB::connection('sqlsrv')
        ->table("Product_4")
        ->select(
            'IDBarang',
            'IDFormularium'
        )
            ->where('IDBarang', $request->IDBarang)
            ->where('IDFormularium', $request->IDFormularium)
            ->get();
    }
    public function deleteBarangFormularium($request)
    {
        return  DB::connection('sqlsrv')->table("Product_4")
        ->where('IDBarang', $request->IDBarang)
            ->where('IDFormularium', $request->IDFormularium)
            ->delete();
    }
    public function getBarangbyFormulariums($id)
    {
        return  DB::connection('sqlsrv')
        ->table("Product_4")
        ->select(
            'Product_4.IDBarang as IDBarang',
            'Product_4.IDFormularium as IDFormularium',
            'TM_FORMULARIUM.Nama_Formularium as Nama_Formularium'
        )
            ->join('TM_FORMULARIUM', 'TM_FORMULARIUM.ID', '=', 'Product_4.IDFormularium')
            ->where('Product_4.IDBarang', $id)->get();
    }
    public function getBarangbyNameLike($request)
    {
        return  DB::connection('sqlsrv')
            ->table("Products")
            ->select(
                'ID',
                'Product Name'
            ) 
            ->where('Group_DK',$request->groupBarang)
             ->where('Product Name', 'like', '%' . $request->name . '%')->get();
            // ->skip(10a0)->take(50)
    }
    public function editHPPBarang($key,$nilaiHppFix)
    {

        $updateBarang =  DB::connection('sqlsrv')->table('Products')
        ->where('ID', $key['ProductCode'])
            ->update([ 
                'NilaiHpp' => $nilaiHppFix
            ]);
        return $updateBarang;
    }
    public function editHPPBarangDoVoidNull($cekDoTerakhirHna, $ProductCode)
    {

        $updateBarang =  DB::connection('sqlsrv')->table('Products')
        ->where('ID', $ProductCode)
        ->update([
            'NilaiHpp' => '0'
        ]);
        return $updateBarang;
    }
    public function editHPPBarangDoVoidNotNull($cekDoTerakhirHna)
    {

        $updateBarang =  DB::connection('sqlsrv')->table('Products')
        ->where('ID', $cekDoTerakhirHna->ProductCode)
            ->update([
                'NilaiHpp' => $cekDoTerakhirHna->Hpp
            ]);
        return $updateBarang;
    }
    public function updateBatalHppbyDo($request)
    {

        $updateBarang =  DB::connection('sqlsrv')->table('Hpps')
        ->where('DeliveryCode', $request->TransactionCode)
            ->update([
                'Batal' => $request->Void,
                'UserBatal' => $request->UserVoid,
                'TglBatal' => $request->DateVoid
            ]);
        return $updateBarang;
    }
    public function updateBatalHnabyDo($request)
    {

        $updateBarang =  DB::connection('sqlsrv')->table('Hnas')
        ->where('DeliveryCode', $request->TransactionCode)
            ->update([
                'Batal' => $request->Void,
                'ExpiredDate' => $request->DateVoid,
                'UserBatal' => $request->UserVoid,
                'TglBatal' => $request->DateVoid
            ]);
        return $updateBarang;
    }

    public function getPrinterLabelAll()
    {
        return  DB::connection('sqlsrv2')
            ->table("SharingPrinter")
            ->select(
            'ID',
            'IP_Komputer',
            'Jenis',
            'IPPrinterSharing',
            'NamaPrinterSharing'
            )
            ->get();
    }
    public function getPrinterLabelbyId($id)
    {
        return  DB::connection('sqlsrv2')
            ->table("SharingPrinter")
            ->select(
                'ID',
                'IP_Komputer',
                'Jenis',
                'IPPrinterSharing',
                'NamaPrinterSharing'
            )
            ->where('ID', $id)
            ->get();
    }
    public function addPrinterLabel($request)
    {
        return  DB::connection('sqlsrv2')->table("SharingPrinter")->insert([
                'IP_Komputer' => $request->IP_Komputer,
                'Jenis' => $request->Jenis,
                'IPPrinterSharing' => $request->IPPrinterSharing,
                'NamaPrinterSharing' => $request->NamaPrinterSharing,
        ]);
    }

    public function editPrinterLabel($request)
    {
        return  DB::connection('sqlsrv2')->table("SharingPrinter")
        ->where('ID', $request->ID)
        ->update([
                'IP_Komputer' => $request->IP_Komputer,
                'Jenis' => $request->Jenis,
                'IPPrinterSharing' => $request->IPPrinterSharing,
                'NamaPrinterSharing' => $request->NamaPrinterSharing,
        ]);
    }
}
