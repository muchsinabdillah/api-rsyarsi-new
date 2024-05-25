<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Service\aBookingBedService;
use App\Http\Repository\aBookingBedRepositoryImpl;

class BookingBedController extends Controller
{
    public function create(Request $request)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->createTrs($request);
    }

    public function edit(Request $request)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->editTrs($request);
    }

    public function void(Request $request)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->voidTrs($request);
    }

    public function view($id)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->view($id);
    }

    public function listAllActive(Request $request)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->getListBookingBedActiveByPeriode($request);
    }

    public function listAllArchive(Request $request)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->getListBookingBedArchiveByPeriode($request);
    }

    public function listAllActivebyNoMR(Request $request)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->getListBookingBedActiveByNoMR($request);
    }

    public function viewByMatch($id,$nomr)
    {
        $aBookingBedRepositoryImpl = new aBookingBedRepositoryImpl();
        $aBookingBedService = new aBookingBedService($aBookingBedRepositoryImpl);
        return $aBookingBedService->viewByMatch($id,$nomr);
    }
    
}
