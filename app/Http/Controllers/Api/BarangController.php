<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repository\aBarangRepositoryImpl;
use App\Http\Repository\aSupplierRepositoryImpl;
use App\Http\Service\aBarangService;
use Illuminate\Http\Request;
use App\Http\Repository\aStokRepositoryImpl;

class BarangController extends Controller
{
    //
    public function addBarang(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->addBarang($request);
        return $addBarang;
    }
    
    public function editBarang(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->editBarang($request);
        return $addBarang;
    }
    public function getBarangAll()
    {
        //
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getBarangAll();
        return $getAllBarang;
    }
    public function getBarangbyId($id)
    {
        //
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getBarangbyId($id);
        return $getAllBarang;
    }
    public function addBarangSupplier(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->addBarangSupplier($request);
        return $addBarang;
    }
    public function deleteBarangSupplier(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->deleteBarangSupplier($request);
        return $addBarang;
    }
    public function getBarangbySuppliers($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getBarangbySuppliers($id);
        return $getAllBarang;
    }
    public function addBarangFormularium(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->addBarangFormularium($request);
        return $addBarang;
    }
    public function deleteBarangFormularium(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->deleteBarangFormularium($request);
        return $addBarang;
    }
    public function getBarangbyFormulariums($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getBarangbyFormulariums($id);
        return $getAllBarang;
    }
  public function getBarangbyNameLike(Request $request)
  {
    $aBarangRepository = new aBarangRepositoryImpl();
    $aSupplierRepository = new aSupplierRepositoryImpl();
    $aStokRepository = new aStokRepositoryImpl();
    $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
    $addBarang =  $aBarangService->getBarangbyNameLike($request);
    return $addBarang;
  }
  
  public function addPrinterLabel(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->addPrinterLabel($request);
        return $addBarang;
    }
    
    public function editPrinterLabel(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->editPrinterLabel($request);
        return $addBarang;
    }
    public function getPrinterLabelAll()
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getPrinterLabelAll();
        return $getAllBarang;
    }
    public function getPrinterLabelbyId($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getPrinterLabelbyId($id);
        return $getAllBarang;
    }
    public function getPrinterbyIp(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getPrinterbyIp($request);
        return $getAllBarang;
    }
    //Unit Farmasi
    public function addIPUnitFarmasi(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->addIPUnitFarmasi($request);
        return $addBarang;
    }
    
    public function editIPUnitFarmasi(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->editIPUnitFarmasi($request);
        return $addBarang;
    }
    public function getIPUnitFarmasiAll()
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getIPUnitFarmasiAll();
        return $getAllBarang;
    }
    public function getIPUnitFarmasibyId($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getIPUnitFarmasibyId($id);
        return $getAllBarang;
    }
    public function getIPUnitFarmasibyIP($ip)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getIPUnitFarmasibyIP($ip);
        return $getAllBarang;
    }
    public function getHistoryHargaBeli($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->getHistoryHargaBeli($id);
        return $addBarang;
    }
    public function getHistoryHargaJual($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->getHistoryHargaJual($id);
        return $addBarang;
    }
    public function getDataPaketbyNameLike(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->getDataPaketbyNameLike($request);
        return $addBarang;
    }
    public function getDataPaketDetailbyIDHdr(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->getDataPaketDetailbyIDHdr($request);
        return $addBarang;
    }
    public function getBarangKonversibyId($id)
    {
        //
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getBarangKonversibyId($id);
        return $getAllBarang;
    }
    public function getBarangKonversibyIddetail($id)
    {
        //
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getBarangKonversibyIddetail($id);
        return $getAllBarang;
    }
    public function addPaketInventory(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->addPaketInventory($request);
        return $addBarang;
    }
    
    public function editPaketInventory(Request $request)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->editPaketInventory($request);
        return $addBarang;
    }
    public function getPaketInventoryAll()
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getPaketInventoryAll();
        return $getAllBarang;
    }
    public function getPaketInventorybyId($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getPaketInventorybyId($id);
        return $getAllBarang;
    }
    public function addDetailPaketInventory(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->addDetailPaketInventory($request);
        return $addBarang;
    }
    public function getDetailPaketInventory($id)
    {
      $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $getAllBarang =  $aBarangService->getDetailPaketInventory($id);
        return $getAllBarang;
    }
    public function deleteDetailPaketInventory(Request $request)
    {
        $aBarangRepository = new aBarangRepositoryImpl();
        $aSupplierRepository = new aSupplierRepositoryImpl();
        $aStokRepository = new aStokRepositoryImpl();
        $aBarangService = new aBarangService($aBarangRepository, $aSupplierRepository,$aStokRepository);
        $addBarang =  $aBarangService->deleteDetailPaketInventory($request);
        return $addBarang;
    }
}
