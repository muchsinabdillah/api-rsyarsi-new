<?php

namespace App\Http\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class bBillingRepositoryImpl implements bBillingRepositoryInterface
{
    public function insertHeader($request,$notrsbilling)
    {
        return  DB::connection('sqlsrv11')->table("FO_T_BILLING")->insert([
            'NO_TRS_BILLING' => $notrsbilling,
            'TGL_BILLING' => $request->TransactionDate,
            'PETUGAS_ENTRY' =>  $request->UserCreate,
            'NO_MR' => $request->NoMr,
            'NO_EPISODE' => $request->NoEpisode,
            'NO_REGISTRASI' => $request->NoRegistrasi,
            'UNIT' =>  $request->UnitTujuan,
            'GROUP_JAMINAN' => $request->GroupJaminan,
            'KODE_JAMINAN' =>  $request->KodeJaminan,
            'TOTAL_TARIF' =>$request->TotalSales,
            'TOTAL_QTY' => $request->TotalQtyOrder, 
            'SUBTOTAL' => $request->SubtotalQtyPrice, 
            'TOTAL_DISCOUNT' => $request->Discount_Prosen, 
            'TOTAL_DISCOUNT_RP' => $request->Discount, 
            'SUBTOTAL_2' => $request->Subtotal, 
            'GRANDTOTAL' => $request->Grandtotal, 
            'BATAL' => '0', 
            'FB_CLOSE_KEUANGAN' => '0', 
            'FB_VERIF_JURNAL' => '1'
        ]);
    }
    public function insertDetail(
                $NO_TRS_BILLING,$TGL_BILLING,$PETUGAS_ENTRY,$NO_MR,
                $NO_EPISODE,$NO_REGISTRASI,$KODE_TARIF,$UNIT,$GROUP_JAMINAN,
                $KODE_JAMINAN,$NAMA_TARIF,$GROUP_TARIF,$KD_KELAS,$QTY,
                $NILAI_TARIF,$SUB_TOTAL,$DISC,$DISC_RP,$SUB_TOTAL_2,$GRANDTOTAL,
                $KODE_REF,$KD_DR,$NM_DR,$GROUP_ENTRI)
    {
        return  DB::connection('sqlsrv11')->table("FO_T_BILLING_1")->insert([
            'NO_TRS_BILLING' => $NO_TRS_BILLING,
            'TGL_BILLING' => $TGL_BILLING,
            'PETUGAS_ENTRY' => $PETUGAS_ENTRY,
            'NO_MR' => $NO_MR,
            'NO_EPISODE' => $NO_EPISODE,
            'NO_REGISTRASI' => $NO_REGISTRASI,
            'KODE_TARIF' => $KODE_TARIF,
            'UNIT' => $UNIT,
            'GROUP_JAMINAN' => $GROUP_JAMINAN,
            'KODE_JAMINAN' => $KODE_JAMINAN,
            'NAMA_TARIF' => $NAMA_TARIF,
            'GROUP_TARIF' => $GROUP_TARIF,
            'KD_KELAS' => $KD_KELAS,
            'QTY' => $QTY,
            'NILAI_TARIF' => $NILAI_TARIF,
            'SUB_TOTAL' => $SUB_TOTAL,
            'DISC' => $DISC,
            'DISC_RP' => $DISC_RP,
            'SUB_TOTAL_2' => $SUB_TOTAL_2,
            'GRANDTOTAL' => $GRANDTOTAL,
            'KODE_REF' => $KODE_REF,
            'KD_DR' => $KD_DR,
            'NM_DR' => $NM_DR,
            'BATAL' => '0',
            'PETUGAS_BATAL' => '',
            'GROUP_ENTRI' => $GROUP_ENTRI,
        ]);
    }
    public function getBillingFo1($request){
        return  DB::connection('sqlsrv11')->table("v_ins_bill2_farmasi")
        ->where('NO_TRS_BILLING', $request->TransactionCode) 
        ->where('GROUP_ENTRI', 'FARMASI')
        ->where('KD_TIPE_PDP', 'OBT1')
        ->get();
    }
    public function insertDetailPdp($request)
    {
        return  DB::connection('sqlsrv11')->table("FO_T_BILLING_2")->insert([
            'ID_BILL' => $request->ID_BILL,  
            'NO_TRS_BILLING' => $request->NO_TRS_BILLING,  
            'KODE_TARIF' => $request->KODE_TARIF,  
            'KODE_KOMPONEN_TARIF' => $request->KD_TIPE_PDP,
            'UNIT' => $request->UNIT,  
            'GROUP_JAMINAN' => $request->GROUP_JAMINAN,  
            'KODE_JAMINAN' => $request->KODE_JAMINAN,  
            'NAMA_TARIF' => $request->NAMA_TARIF,  
            'GROUP_TARIF' => $request->GROUP_TARIF,  
            'KD_KELAS' => $request->KELAS,  
            'QTY' => $request->QTY,  
            'NILAI_TARIF' => $request->NILAI_TARIF,  
            'SUB_TOTAL' => $request->SUBTOTAL,  
            'DISC' => $request->DISC,  
            'DISC_RP' => $request->DISC_RP,  
            'SUB_TOTAL_2' => $request->SUB_TOTAL_PDP_2,  
            'NILAI_DISKON_PDP' => $request->NILAI_DISKON_PDP,  
            'NILAI_PDP' => $request->NILAI_PDP,  
            'KD_DR' => $request->KD_DR,  
            'NM_DR' => $request->NM_DR,  
            'NILAI_PROSEN' => $request->NILAI_PROSEN,  
            'BATAL' => $request->BATAL,  
            'PETUGAS_BATAL' => $request->PETUGAS_BATAL,  
            'JAM_BATAL' => $request->JAM_BATAL,  
            'KD_POSTING' => $request->KD_POSTING,  
            'KD_POSTING_DISC' => $request->kd_posting_diskon,  
            'ID_TR_TARIF_PAKET' => $request->ID_TR_TARIF_PAKET    

        ]);

    }

    public function voidBillingPasien($request)
    {
        $updatesatuan =  DB::connection('sqlsrv11')->table('FO_T_BILLING')
            ->where('NO_TRS_BILLING', $request->TransactionCode)
            ->where('Batal', "0")
            ->update([
                'Batal' => $request->Void,
                'JAM_BATAL' => Carbon::now(),
                'PETUGAS_BATAL' => $request->UserVoid,
                'ALASAN_BATAL' => $request->ReasonVoid
            ]);
        return $updatesatuan;
    }
    public function voidBillingPasienOne($request)
    {
        $updatesatuan =  DB::connection('sqlsrv11')->table('FO_T_BILLING_1')
            ->where('NO_TRS_BILLING', $request->TransactionCode)
            ->where('Batal', "0")
            ->update([
                'Batal' => $request->Void,
                'JAM_BATAL' => Carbon::now(),
                'PETUGAS_BATAL' => $request->UserVoid,
            ]);
        return $updatesatuan;
    }
    public function voidBillingPasienTwo($request)
    {
        $updatesatuan =  DB::connection('sqlsrv11')->table('FO_T_BILLING_2')
            ->where('NO_TRS_BILLING', $request->TransactionCode)
            ->where('Batal', "0")
            ->update([
                'Batal' => $request->Void,
                'JAM_BATAL' => Carbon::now(),
                'PETUGAS_BATAL' => $request->UserVoid,
            ]);
        return $updatesatuan;
    }

    public function updateBillingPasienbyNoTRS($request)
    {
        $updatesatuan =  DB::connection('sqlsrv11')->table('FO_T_BILLING')
            ->where('NO_TRS_BILLING', $request->TransactionCode)
            ->where('Batal', "0")
            ->update([
                'NO_TRS_BILLING' => $request->TransactionCode,
                'UNIT' =>  $request->UnitTujuan,
                'GROUP_JAMINAN' => $request->GroupJaminan,
                'TOTAL_TARIF' =>$request->TotalSales,
                'TOTAL_QTY' => $request->TotalQtyOrder, 
                'SUBTOTAL' => $request->SubtotalQtyPrice, 
                'TOTAL_DISCOUNT' => $request->Discount, 
                'TOTAL_DISCOUNT_RP' => $request->Discount, 
                'SUBTOTAL_2' => $request->Subtotal, 
                'GRANDTOTAL' => $request->Grandtotal, 
                'FB_CLOSE_KEUANGAN' => '0', 
                'FB_VERIF_JURNAL' => '1'
            ]);
        return $updatesatuan;
    }
}