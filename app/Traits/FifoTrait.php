<?php

namespace App\Traits;

use Exception;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

trait FifoTrait
{
    public function fifoOut($request,$key,$tipeTrs){
        
        // get Hpp Average 
        $getHppBarang = $this->ahnaRepository->getHppAverage($key)->first()->first();
        $xhpp = $getHppBarang->NominalHpp;
        // get Hpp Average

        // CARI DO TERATAS YANG MASIH ADA QTY
        first:
        $getStokFirst = $this->aStok->getStokExpiredFirstGlobal($request, $key,$request->UnitCode);
     
        //return $getStokFirst;
        $DeliveryCodex = $getStokFirst->DeliveryCode;
        
        $qtyBuku = $getStokFirst->x;
        $ExpiredDate = $getStokFirst->ExpiredDate;
        $BatchNumber = $getStokFirst->BatchNumber;

        if ($qtyBuku < $key['Konversi_QtyTotal']) {
            $qtynew = $qtyBuku;
            $persediaan = $qtynew * $xhpp;
        } else {
            $qtynew = $key['Konversi_QtyTotal'];
            $persediaan = $qtynew * $xhpp;
        }

        // update stok Tujuan / Gudang 
        // if ($this->aStok->cekStokbyIDBarang($key, $request->UnitCode)->count() < 1) {
            //kalo g ada insert
            // $this->aStok->addStokTrs($request, $key, $qtynew, $request->UnitCode);
        // } else {
            //kallo ada ya update
            // $sumstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitCode);
            // foreach ($sumstok as $value) {
            //     $QtyCurrent = $value->Qty;
            // }
            // $QtyTotal = $QtyCurrent - $qtynew;
            // $this->aStok->updateStokTrs($request, $key, $QtyTotal, $request->UnitCode);
        // }
        if ($qtynew < $key['Konversi_QtyTotal']) {
            $key['Konversi_QtyTotal'] = $key['Konversi_QtyTotal'] - $qtynew;
            goto first;
        }
        
          // // INSERT BUKU IN DI LAYANAN GUDANG
          $this->aStok->addBukuStokOut($request, $key, $tipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);
          $this->aStok->addDataStokOut($request, $key, $tipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);

    }
    public function fifoMutasi($request,$key){
        // delete tabel buku 
        $getHppBarang = $this->ahnaRepository->getHppAverage($key)->first()->first();
        $xhpp = $getHppBarang->NominalHpp;
                        // CARI DO TERATAS YANG MASIH ADA QTY
                        first:
                        $getStokFirst = $this->aStokRepository->getStokExpiredFirst($request, $key);
                     
                        //return $getStokFirst;
                        $DeliveryCodex = $getStokFirst->DeliveryCode;
                        
                        $qtyBuku = $getStokFirst->x;
                        $ExpiredDate = $getStokFirst->ExpiredDate;
                        $BatchNumber = $getStokFirst->BatchNumber;

                        if ($qtyBuku < $key['Konversi_QtyTotal']) {
                            $qtynew = $qtyBuku;
                            $persediaan = $qtynew * $xhpp;
                        } else {
                            $qtynew = $key['Konversi_QtyTotal'];
                            $persediaan = $qtynew * $xhpp;
                        }
                        $TipeTrs = "MT";
                        // // INSERT BUKU IN DI LAYANAN GUDANG
                        $this->aStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);
                        $this->aStokRepository->addDataStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);

                        // // update stok Tujuan / Gudang 
                        // if ($this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan)->count() < 1) {
                        //     //kalo g ada insert
                        //     $this->aStokRepository->addStokTrs($request, $key, $qtynew, $request->UnitTujuan);
                        // } else {
                            //kallo ada ya update
                            // $sumstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitTujuan);
                            // foreach ($sumstok as $value) {
                            //     $QtyCurrent = $value->Qty;
                            // }
                            // $QtyTotal = $QtyCurrent - $qtynew;
                            // $this->aStokRepository->updateStokTrs($request, $key, $QtyTotal, $request->UnitTujuan);
                        // }

                        //insert stok Lokasi order 
                        if ($request->JenisStok == "STOK") {
                            // INSERT BUKU IN DI LAYANAN TUJUAN
                            $this->aStokRepository->addBukuStokIn($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);
                            $this->aStokRepository->addDataBukuStokIn($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);

                            // if ($this->aStokRepository->cekStokbyIDBarang($key, $request->UnitOrder)->count() < 1) {
                            //     // kalo g ada insert
                            //     $this->aStokRepository->addStokTrs($request, $key, $qtynew, $request->UnitOrder);
                            // } else {
                            //     // kallo ada ya update
                            //     $sumstok2 = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitOrder);
                            //     foreach ($sumstok2 as $value2) {
                            //         $QtyCurrent2 = $value2->Qty;
                            //     }
                            //     $QtyTotal2 = $QtyCurrent2+$qtynew;
                            //    $this->aStokRepository->updateStokTrs($request, $key, $QtyTotal2, $request->UnitOrder);
                            // }

                        } else {

                            $this->aStokRepository->addBukuStokIn($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);
                            $this->aStokRepository->addDataBukuStokIn($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);
                            $this->aStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);
                            $this->aStokRepository->addDataStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitOrder);
                       
                        }

                        if ($qtynew < $key['Konversi_QtyTotal']) {
                            $key['Konversi_QtyTotal'] = $key['Konversi_QtyTotal'] - $qtynew;
                            goto first;
                        }
    }
    public function fifoConsumable($request,$key,$xhpp){
        // QUERY PENGURANGAN STOK METODE FIFO
        first:
        $getStokFirst = $this->aStok->getStokExpiredFirst($request, $key);
        
        //  return $getStokFirst;
        $DeliveryCodex = $getStokFirst->DeliveryCode;
        //$xhpp = $getStokFirst->Hpp;
        $qtyBuku = $getStokFirst->x;
        $ExpiredDate = $getStokFirst->ExpiredDate;
        $BatchNumber = $getStokFirst->BatchNumber;

        if ($qtyBuku < $key['Konversi_QtyTotal']) {
            $qtynew = $qtyBuku;
            $persediaan = $qtynew * $xhpp;
        } else {
            $qtynew = $key['Konversi_QtyTotal'];
            $persediaan = $qtynew * $xhpp;
        }
        $TipeTrs = "CM";
        // // INSERT BUKU IN DI LAYANAN GUDANG
        $this->aStok->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);
        $this->aStok->addDataStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);

        // update stok Tujuan / Gudang 
        //if ($this->aStok->cekStokbyIDBarang($key, $request->UnitTujuan)->count() < 1) {
            //kalo g ada insert
           // $this->aStok->addStokTrs($request, $key, $qtynew, $request->UnitTujuan);
       // } else {
            //kallo ada ya update
           // $sumstok = $this->aStok->cekStokbyIDBarang($key, $request->UnitTujuan);
           // foreach ($sumstok as $value) {
            //    $QtyCurrent = $value->Qty;
           // }
            //$QtyTotal = $QtyCurrent - $qtynew;
           // $this->aStok->updateStokTrs($request, $key, $QtyTotal, $request->UnitTujuan);
       // }

        if ($qtynew < $key['Konversi_QtyTotal']) {
            $key['Konversi_QtyTotal'] = $key['Konversi_QtyTotal'] - $qtynew;
            goto first;
        }
        // QUERY PENGURANGAN STOK METODE FIFO
    }
    public function fifoAdjusmentMinus($request,$key,$qty){
        // delete tabel buku  
        $xhpp = $key['Hpp'];
                        // CARI DO TERATAS YANG MASIH ADA QTY
                        first: 
                        $getStokFirst = $this->aStokRepository->getStokExpiredFirstGlobal($request, $key,$request->UnitCode);
                        //return $getStokFirst;
                        $DeliveryCodex = $getStokFirst->DeliveryCode;
                        
                        $qtyBuku = $getStokFirst->x;
                        $ExpiredDate = $getStokFirst->ExpiredDate;
                        $BatchNumber = $getStokFirst->BatchNumber;

                        if ($qtyBuku < $qty) {
                            $qtynew = $qtyBuku;
                            $persediaan = $qtynew * $xhpp;
                        } else {
                            $qtynew = $qty;
                            $persediaan = $qtynew * $xhpp;
                        }
                        $TipeTrs = "AD";
                        // // INSERT BUKU IN DI LAYANAN GUDANG
                        $this->aStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);
                        $this->aStokRepository->addDataStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);
                        if ($qtynew < $qty) {
                            $qty = $qty - $qtynew;
                            goto first;
                        }
    }
    public function fifoAdjusmentPlus($request,$key,$qty){
        // delete tabel buku  
        $xhpp = $key['Hpp'];
                        // CARI DO TERATAS YANG MASIH ADA QTY
                        $persediaan = $xhpp*$qty;              
                        $TipeTrs = "AD";
                        // // INSERT BUKU IN DI LAYANAN GUDANG
                        $this->aStokRepository->addBukuStokIn($request, $key, $TipeTrs, $request->TransactionCode, $xhpp, $key['ExpiredDate'], $key['Batchnumber'], $qty, $persediaan, $request->UnitCode);
                        $this->aStokRepository->addDataBukuStokIn($request, $key, $TipeTrs, $request->TransactionCode, $xhpp, $key['ExpiredDate'], $key['Batchnumber'], $qty, $persediaan, $request->UnitCode);

                        // update stok Tujuan / Gudang 
                       // if ($this->aStokRepository->cekStokbyIDBarang($key, $request->UnitCode)->count() < 1) {
                            //kalo g ada insert
                           // $this->aStokRepository->addStokTrs($request, $key, $qty, $request->UnitCode);
                       // } else {
                            //kallo ada ya update
                            //$sumstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitCode);
                            // foreach ($sumstok as $value) {
                            //     $QtyCurrent = $value->Qty;
                            // }
                           // $QtyTotal = $QtyCurrent + $qty;
                            ///$this->aStokRepository->updateStokTrs($request, $key, $QtyTotal, $request->UnitCode);
                       // }
    }
    public function fifoSales($request,$key,$xhpp){
        // QUERY PENGURANGAN STOK METODE FIFO
        first:
        $getStokFirst = $this->sStokRepository->getStokExpiredFirst($request, $key);
     
        //  return $getStokFirst;
        $DeliveryCodex = $getStokFirst->DeliveryCode;
        $qtyBuku = $getStokFirst->x;
        $ExpiredDate = $getStokFirst->ExpiredDate;
        $BatchNumber = $getStokFirst->BatchNumber;

        if ($qtyBuku < $key['Qty']) {
            $qtynew = $qtyBuku;
            $persediaan = $qtynew * $xhpp;
        } else {
            $qtynew = $key['Qty'];
            $persediaan = $qtynew * $xhpp;
        }
        $TipeTrs = "TPR";
        // // INSERT BUKU IN DI LAYANAN GUDANG
        $this->sStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);
        $this->sStokRepository->addDataStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitTujuan);

        if ($qtynew < $key['Qty']) {
            $key['Qty'] = $key['Qty'] - $qtynew;
            goto first;
        }
        // QUERY PENGURANGAN STOK METODE FIFO
    }
    public function fifoReturJual($request,$key,$tipeTrs){
        
        // get Hpp Average 
        $getHppBarang = $this->ahnaRepository->getHppAverage($key)->first()->first();
        $xhpp = $getHppBarang->NominalHpp;
        // get Hpp Average

        // CARI DO TERATAS YANG MASIH ADA QTY
        first:
        $getStokFirst = $this->aStok->getStokExpiredFirstGlobal($request, $key,$request->UnitCode);
     
        //return $getStokFirst;
        $DeliveryCodex = $getStokFirst->DeliveryCode;
        
        $qtyBuku = $getStokFirst->x;
        $ExpiredDate = $getStokFirst->ExpiredDate;
        $BatchNumber = $getStokFirst->BatchNumber;

        if ($qtyBuku < $key['Konversi_QtyTotal']) {
            $qtynew = $qtyBuku;
            $persediaan = $qtynew * $xhpp;
        } else {
            $qtynew = $key['Konversi_QtyTotal'];
            $persediaan = $qtynew * $xhpp;
        }
        if ($qtynew < $key['Konversi_QtyTotal']) {
            $key['Konversi_QtyTotal'] = $key['Konversi_QtyTotal'] - $qtynew;
            goto first;
        }
        
          // // INSERT BUKU IN DI LAYANAN GUDANG
          $this->aStok->addBukuStokIn($request, $key, $tipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);
          $this->aStok->addDataBukuStokIn($request, $key, $tipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);

    }


    public function fifoAdjusmentMinusAdjusmentStokOnlyMigration($request,$key,$qty){
        // delete tabel buku  
        $xhpp = $key['Hpp'];
                        // CARI DO TERATAS YANG MASIH ADA QTY
                        first: 
                        $getStokFirst = $this->aStokRepository->getStokExpiredFirstGlobal($request, $key,$request->UnitCode);
                        //return $getStokFirst;
                        $DeliveryCodex = $getStokFirst->DeliveryCode;
                        
                        $qtyBuku = $getStokFirst->x;
                        $ExpiredDate = $getStokFirst->ExpiredDate;
                        $BatchNumber = $getStokFirst->BatchNumber;

                        if ($qtyBuku < $qty) {
                            $qtynew = $qtyBuku;
                            $persediaan = $qtynew * $xhpp;
                        } else {
                            $qtynew = $qty;
                            $persediaan = $qtynew * $xhpp;
                        }
                        $TipeTrs = "AD";
                        // // INSERT BUKU IN DI LAYANAN GUDANG
                        $this->aStokRepository->addBukuStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);
                        $this->aStokRepository->addDataStokOut($request, $key, $TipeTrs, $DeliveryCodex, $xhpp, $ExpiredDate, $BatchNumber, $qtynew, $persediaan, $request->UnitCode);
                        if ($qtynew < $qty) {
                            $qty = $qty - $qtynew;
                            goto first;
                        }
    }
    public function fifoAdjusmentPlusAdjusmentStokOnlyMigration($request,$key,$qty){
        // delete tabel buku  
        $xhpp = $key['Hpp'];
                        // CARI DO TERATAS YANG MASIH ADA QTY
                        $persediaan = $xhpp*$qty;              
                        $TipeTrs = "AD";
                        // // INSERT BUKU IN DI LAYANAN GUDANG
                        $this->aStokRepository->addBukuStokIn($request, $key, $TipeTrs, $request->TransactionCode, $xhpp, $key['ExpiredDate'], $key['Batchnumber'], $qty, $persediaan, $request->UnitCode);
                        $this->aStokRepository->addDataBukuStokIn($request, $key, $TipeTrs, $request->TransactionCode, $xhpp, $key['ExpiredDate'], $key['Batchnumber'], $qty, $persediaan, $request->UnitCode);

                        // update stok Tujuan / Gudang 
                       // if ($this->aStokRepository->cekStokbyIDBarang($key, $request->UnitCode)->count() < 1) {
                            //kalo g ada insert
                           // $this->aStokRepository->addStokTrs($request, $key, $qty, $request->UnitCode);
                       // } else {
                            //kallo ada ya update
                            //$sumstok = $this->aStokRepository->cekStokbyIDBarang($key, $request->UnitCode);
                            // foreach ($sumstok as $value) {
                            //     $QtyCurrent = $value->Qty;
                            // }
                           // $QtyTotal = $QtyCurrent + $qty;
                            ///$this->aStokRepository->updateStokTrs($request, $key, $QtyTotal, $request->UnitCode);
                       // }
    }
}
