<?php

namespace App\Traits;

trait HargaJualTrait
{
    public function HargaJual($GroupJaminan,$noregistrasi,$Hna,$Category,$kelas,$Konversi_satuan){

        $hargaPlusppn =  $Hna + (($Hna*11)/100);
        if($GroupJaminan == "UM"){
            if(substr($noregistrasi,1,2) == "RJ"  ) {
                 $hargadasar = $hargaPlusppn;
                 $hargaprofit = $hargadasar*1.3; 
             }elseif(substr($noregistrasi,1,2) == "RI"){
                 $hargadasar = $hargaPlusppn;
                 $hargaprofit = $hargadasar*1.4; 
             }else{
                $hargadasar = $hargaPlusppn;
                $hargaprofit = $hargadasar*1.3; 
             }
         }

         if($GroupJaminan == "TE"){
             if($hargaPlusppn >= 250000){
                 $hargadiskon = ($hargaPlusppn * 20) / 100;
                 $hargaprofit =$hargaPlusppn-$hargadiskon;
             }else{
                 if($Category == "OBAT GENERIK"){
                     $hargaprofit = $hargaPlusppn * 1.2;
                 }else if($Category == "OBAT NON GENERIK" || $Category = "NON GENERIK"  ){
                     $hargaprofit = $hargaPlusppn * 1.18;
                 }else if($Category == "ALAT KESEHATAN"){
                     $hargabefore = ($hargaPlusppn * 20) / 100;
                     $hargaprofit = $hargaPlusppn - $hargabefore;
                 }
             }
         }
         
         if($GroupJaminan == "IH"){
             if($hargaPlusppn <= '50000' ){
                 $hargaprofit = ($hargaPlusppn  * 1.2);
             }else if($hargaPlusppn >= '50000' && $hargaPlusppn <= '250000' ){
                 $hargaprofit = ($hargaPlusppn  * 1.15);
             }else if($hargaPlusppn >= '250000' && $hargaPlusppn <= '500000' ){
                 $hargaprofit = ($hargaPlusppn  * 1.1);
             }else if($hargaPlusppn >= '500000'  ){
                 $hargaprofit = ($hargaPlusppn  * 1.05);
             }
         }

         if($GroupJaminan == "BS"){
             if(substr($noregistrasi,1,2) == "RJ"  ) {
                  $hargaprofit = $hargaPlusppn; 
              }elseif(substr($noregistrasi,1,2) == "RI"){
                 if($kelas == "3")   {
                     $hargaprofit = $hargaPlusppn; 
                 }else if ($kelas == "2") {
                     $hargaprofit = $hargaPlusppn * 1.2; 
                 } else if ($kelas == "1") {
                     $hargaprofit = $hargaPlusppn * 1.4; 
                 } else  {
                     $hargaprofit = $hargaPlusppn * 1.4; 
                 }  
              }else{
                $hargaprofit = $hargaPlusppn; 
              }
          }

         $harga = $hargaprofit+400+400;
         return $harga;
    }
}