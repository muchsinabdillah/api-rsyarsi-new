<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Service\aBarangService;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    //
    public function addBarang(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->addBarang($request);
        return $addBarang;
    }
    
    public function editBarang(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->editBarang($request);
        return $addBarang;
    }
    public function getBarangAll()
    {
        //
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getBarangAll();
        return $getAllBarang;
    }
    public function getBarangbyId($id)
    {
        //
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getBarangbyId($id);
        return $getAllBarang;
    }
    public function addBarangSupplier(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->addBarangSupplier($request);
        return $addBarang;
    }
    public function deleteBarangSupplier(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->deleteBarangSupplier($request);
        return $addBarang;
    }
    public function getBarangbySuppliers($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getBarangbySuppliers($id);
        return $getAllBarang;
    }
    public function addBarangFormularium(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->addBarangFormularium($request);
        return $addBarang;
    }
    public function deleteBarangFormularium(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->deleteBarangFormularium($request);
        return $addBarang;
    }
    public function getBarangbyFormulariums($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getBarangbyFormulariums($id);
        return $getAllBarang;
    }
  public function getBarangbyNameLike(Request $request)
  {
    $aBarangRepository = new aBarangRepositoryImpl();
    $aSupplierRepository = new aSupplierRepositoryImpl();
    $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
    $addBarang =  $aBarangService->getBarangbyNameLike($request);
    return $addBarang;
  }
  
  public function addPrinterLabel(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->addPrinterLabel($request);
        return $addBarang;
    }
    
    public function editPrinterLabel(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->editPrinterLabel($request);
        return $addBarang;
    }
    public function getPrinterLabelAll()
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getPrinterLabelAll();
        return $getAllBarang;
    }
    public function getPrinterLabelbyId($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getPrinterLabelbyId($id);
        return $getAllBarang;
    }

    //Unit Farmasi
    public function addIPUnitFarmasi(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->addIPUnitFarmasi($request);
        return $addBarang;
    }
    
    public function editIPUnitFarmasi(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->editIPUnitFarmasi($request);
        return $addBarang;
    }
    public function getIPUnitFarmasiAll()
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getIPUnitFarmasiAll();
        return $getAllBarang;
    }
    public function getIPUnitFarmasibyId($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getIPUnitFarmasibyId($id);
        return $getAllBarang;
    }
    public function getIPUnitFarmasibyIP($ip)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getIPUnitFarmasibyIP($ip);
        return $getAllBarang;
    }
    public function getHistoryHargaBeli($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->getHistoryHargaBeli($id);
        return $addBarang;
    }
    public function getHistoryHargaJual($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->getHistoryHargaJual($id);
        return $addBarang;
    }
    public function getDataPaketbyNameLike(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->getDataPaketbyNameLike($request);
        return $addBarang;
    }
    public function getDataPaketDetailbyIDHdr(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $addBarang =  $aBarangService->getDataPaketDetailbyIDHdr($request);
        return $addBarang;
    }
    public function getBarangKonversibyId($id)
    {
        //
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository);
        $getAllBarang =  $aBarangService->getBarangKonversibyId($id);
        return $getAllBarang;
    }
}
