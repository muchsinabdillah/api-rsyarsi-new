<?php
namespace App\Http\Repository;
interface bPaymentRepositoryInterface
{
    public function createHdr($request,$autonumber);
    //public function editTrs($request);
    //public function getTrsBookingBedByTrsCode($id);  

}