<?php
namespace App\Http\Repository;
interface aBookingBedRepositoryInterface
{
    public function createTrs($request,$autonumber);
    public function editTrs($request);
    //public function getTrsBookingBedByTrsCode($id);  

}