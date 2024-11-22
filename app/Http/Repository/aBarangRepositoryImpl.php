<?php

namespace App\Http\Repository;

use Carbon\Carbon;
use App\Models\gallery;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

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
            'JenisBarang' => $request->Jenis_Barang,
            'Golongan' => $request->Golongan_Obat,
            'Group_DK' => $request->Group_DK,
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
            'JenisBarang' => $request->Jenis_Barang,
            'Golongan' => $request->Golongan_Obat,
            'Group_DK' => $request->Group_DK,
            'KD_PDP' => $request->KD_PDP,
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
            'Kode_Barcode' ,
            'Golongan',
            'KD_PDP',
            'JenisBarang'
            )
            ->where('ID', $id)
            ->get();
    }
    public function getBarangbyIdandgolongan($id)
    {
        return  DB::connection('sqlsrv')
            ->table("Products")
            ->select( 
            'Category','Konversi_satuan'
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
    public function getBarangbyNameLikeAdjusment($request)
    {
        return  DB::connection('sqlsrv')
            ->table("v_stok_adjusment")
            ->select(
                'ID',
                'Product Name',
                'Standard Cost',
                'HargaBeli',
                'Satuan_Beli',
                'Qty',
                'Unit Satuan',
                'Konversi_satuan'
            )  
             ->where('Product Name', 'like', '%' . $request->name . '%')->get(); 
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
            'NamaPrinterSharing',
            'Hostname',
            'UserAccount',
            'PasswordAccount'
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
                'NamaPrinterSharing',
                'Hostname',
                'UserAccount',
                'PasswordAccount'
            )
            ->where('ID', $id)
            ->get();
    }
    public function getPrinterbyIp($request)
    {
        return  DB::connection('sqlsrv2')
            ->table("SharingPrinter")
            ->select(
                'ID',
                'IP_Komputer',
                'Jenis',
                'IPPrinterSharing',
                'NamaPrinterSharing',
                'Hostname',
                'UserAccount',
                'PasswordAccount'
            )
            ->where('IP_Komputer', $request->IP_Komputer)
            ->where('Jenis', $request->Jenis)
            ->get();
    }
    public function addPrinterLabel($request)
    {
        return  DB::connection('sqlsrv2')->table("SharingPrinter")->insert([
                'IP_Komputer' => $request->IP_Komputer,
                'Jenis' => $request->Jenis,
                'IPPrinterSharing' => $request->IPPrinterSharing,
                'NamaPrinterSharing' => $request->NamaPrinterSharing,
                'Hostname' => $request->Hostname,
                'UserAccount' => $request->UserAccount,
                'PasswordAccount' => base64_encode($request->PasswordAccount)

                
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
                'Hostname' => $request->Hostname,
                'UserAccount' => $request->UserAccount,
                'PasswordAccount' => base64_encode($request->PasswordAccount)
        ]);
    }

    //Master Unit Farmasi
    public function getIPUnitFarmasiAll()
    {
        return  DB::connection('sqlsrv')
            ->table("MasterIPUnit")
            ->select(
            'ID',
            'IPAddress',
            'UnitCode'
            )
            ->get();
    }
    public function getIPUnitFarmasibyId($id)
    {
        return  DB::connection('sqlsrv')
            ->table("MasterIPUnit")
            ->select(
                'ID',
                'IPAddress',
                'UnitCode'
            )
            ->where('ID', $id)
            ->get();
    }
    public function addIPUnitFarmasi($request)
    {
        return  DB::connection('sqlsrv')->table("MasterIPUnit")->insert([
                'IPAddress' => $request->IPAddress,
                'UnitCode' => $request->UnitCode,
        ]);
    }

    public function editIPUnitFarmasi($request)
    {
        return  DB::connection('sqlsrv')->table("MasterIPUnit")
        ->where('ID', $request->ID)
        ->update([
                'IPAddress' => $request->IPAddress,
                'UnitCode' => $request->UnitCode,
        ]);
    }
    public function getIPUnitFarmasibyIP($request)
    {
        return  DB::connection('sqlsrv')->table("MasterIPUnit")
            ->where('IPAddress', $request)
            ->get();
    }
    public function getHistoryHargaBeli($id)
    {
        return  DB::connection('sqlsrv')->table("Hpps")
            ->where('ProductCode', $id)
            ->select(
                'id',
                'DeliveryCode',
                'DeliveryDate',
                'NominalHargabeli',
                'NominalDiskon',
                'NominalHpp',
                'UserCreate',
                DB::raw("CASE WHEN Batal = '1' then 'BATAL' ELSE 'TIDAK BATAL' END AS Status"),
                'UserBatal'
            )
            ->get();
    }

    public function getHistoryHargaJual($id)
    {
        return  DB::connection('sqlsrv')->table("Hnas")
            ->where('ProductCode', $id)
            ->select(
                'id',
                'DeliveryCode',
                'DeliveryDate',
                'NominalHna',
                'NominalHnaMinDiskon',
                'UserCreate',
                'StartDate',
                'ExpiredDate',
                DB::raw("CASE WHEN Batal = '1' then 'BATAL' ELSE 'TIDAK BATAL' END AS Status"),
                'UserBatal',
                'TglBatal'
            )
            ->get();
    }

    public function insertLog($TransactionType,$TransactionNumber,$UserCreate,$Reasons)
    {
        return  DB::connection('sqlsrv')->table("LogInventories")->insert([
            'TransactionType' => $TransactionType,
            'TransactionNumber' => $TransactionNumber,
            'UserCreate' => $UserCreate,
            'DateCreate' => Carbon::now(),
            'Reasons' => $Reasons,
        ]);
    } 
    public function getDataPaketbyNameLike($keywords)
    {
        return  DB::connection('sqlsrv')->table("PaketItems")
        ->where('nama_paket', 'like', '%' . $keywords . '%')
            ->get();
    }
    
    public function getDataPaketDetailbyIDHdr($id_header)
    {
        return  DB::connection('sqlsrv')->table("PaketItemDetails")
        ->where('id_header', $id_header)
        ->where('status','1')
            ->get();
    }
    public function getBarangKonversibyId($id)
    {
        return  DB::connection('sqlsrv')
            ->table("ProductKonversis")
            ->select(
            'id',
            'IdBarang',
            'SatuanBeli', 
            'SatuanJual',
            'NilaiKonversi' 
            )
            ->where('idBarang', $id)
            ->get();
    }
    public function getBarangKonversibyIddetail($id)
    {
        return  DB::connection('sqlsrv')
            ->table("ProductKonversis")
            ->select(
            'id',
            'IdBarang',
            'SatuanBeli', 
            'SatuanJual',
            'NilaiKonversi' 
            )
            ->where('Id', $id)
            ->get();
    }
    public function getPaketInventoryAll()
    {
        return  DB::connection('sqlsrv')
            ->table("PaketItems")
            ->get();
    }
    public function getPaketInventorybyId($id)
    {
        return  DB::connection('sqlsrv')
            ->table("PaketItems")
            ->where('ID', $id)
            ->get();
    }
    public function addPaketInventory($request)
    {
        return  DB::connection('sqlsrv')->table("PaketItems")->insertGetId([
                'nama_paket' => $request->nama_paket,
                'user_create' => $request->user_create,
                'date_create' => Carbon::now(),
                'status' => '1',
        ]);
    }

    public function editPaketInventory($request)
    {
        return  DB::connection('sqlsrv')->table("PaketItems")
        ->where('ID', $request->ID)
        ->update([
            'nama_paket' => $request->nama_paket,
            'user_update' => $request->user_update,
            'date_update' => Carbon::now(),
            'status' => $request->status,
        ]);
    }
    public function getPaketInventorybyName($nama_paket)
    {
        return  DB::connection('sqlsrv')
            ->table("PaketItems")
            ->where('nama_paket', $nama_paket)
            ->get();
    }
    public function getItemsDoublePaket($request)
    {
        return  DB::connection('sqlsrv')->table("PaketItemDetails")
        ->where('id_header', $request->IDHeader)
            ->where('status', '1')
            ->where('product_id', $request->ProductCode)
            ->get();
    } 
    public function addPaketDetil($request)
    {
        return  DB::connection('sqlsrv')->table("PaketItemDetails")->insert([
            'id_header' => $request->IDHeader,
            'product_id' => $request->ProductCode,
            'nama_product' => $request->ProductName,
            'quantity' => $request->QtyPR,
            'status' => '1',
        ]);
    }
    public function getDetailPaketInventorybyIDHdr($id)
    {
        return  DB::connection('sqlsrv')
            ->table("PaketItemDetails")
            ->where('id_header', $id)
            ->where('status', '1')
            ->get();
    }
    public function deleteDetailPaketInventory($request)
    {
        return  DB::connection('sqlsrv')->table("PaketItemDetails")
        ->where('product_id', $request->ProductCode)
        ->where('id_header', $request->IDHeader)
        ->where('status', '1')
        ->update([
            'status' => '0',
        ]);
    }
}
