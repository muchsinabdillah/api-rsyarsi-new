<?php

namespace App\Traits;

use Exception;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

trait HargaJualTrait
{
    public function HargaJual($GroupJaminan,$noregistrasi,$Hna,$Category,$kelas){

 
        if($GroupJaminan == "UM"){
            if(substr($noregistrasi,1,2) == "RJ"  ) {
                 $hargadasar = $Hna;
                 $hargaprofit = $hargadasar*1.3; 
             }else{
                 $hargadasar = $Hna;
                 $hargaprofit = $hargadasar*1.4; 
             }
         }

         if($GroupJaminan == "TE"){
             if($Hna >= 250000){
                 $hargadiskon = ($Hna * 20) / 100;
                 $hargaprofit =$Hna-$hargadiskon;
             }else{
                 if($Category == "OBAT GENERIK"){
                     $hargaprofit = $Hna * 1.2;
                 }else if($Category == "OBAT NON GENERIK" || $Category = "NON GENERIK"  ){
                     $hargaprofit = $Hna * 1.18;
                 }else if($Category == "ALAT KESEHATAN"){
                     $hargabefore = ($Hna * 20) / 100;
                     $hargaprofit = $Hna - $hargabefore;
                 }
             }
         }
         
         if($GroupJaminan == "IH"){
             if($Hna <= '50000' ){
                 $hargaprofit = ($Hna  * 1.2);
             }else if($Hna >= '50000' && $Hna <= '250000' ){
                 $hargaprofit = ($Hna  * 1.15);
             }else if($Hna >= '250000' && $Hna <= '500000' ){
                 $hargaprofit = ($Hna  * 1.1);
             }else if($Hna >= '500000'  ){
                 $hargaprofit = ($Hna  * 1.05);
             }
         }

         if($GroupJaminan == "BS"){
             if(substr($noregistrasi,1,2) == "RJ"  ) {
                  $hargaprofit = $Hna; 
              }else{
                 if($kelas == "3")   {
                     $hargaprofit = $Hna; 
                 }else if ($kelas == "2") {
                     $hargaprofit = $Hna * 1.2; 
                 } else if ($kelas == "1") {
                     $hargaprofit = $Hna * 1.4; 
                 } else  {
                     $hargaprofit = $Hna * 1.4; 
                 }  
              }
          }

         $harga = $hargaprofit+400+400;
         return $harga;
    }
}