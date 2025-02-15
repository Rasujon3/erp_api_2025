<?php

namespace App\Modules\Warehouses\Controllers;

use App\Modules\Divisions\Queries\DivisionDatatable;
use App\Modules\Divisions\Repositories\DivisionRepository;
use App\Modules\Divisions\Requests\DivisionRequest;
use App\Modules\Warehouses\Queries\WarehouseDatatable;
use App\Modules\Warehouses\Repositories\WarehouseRepository;
use App\Modules\Warehouses\Requests\WarehouseRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class WarehouseController extends AppBaseController
{
    protected WarehouseRepository $warehouseRepository;
    protected WarehouseDatatable $warehouseDatatable;

    public function __construct(WarehouseRepository $warehouseRepo, WarehouseDatatable $warehouseDatatable)
    {
        $this->warehouseRepository = $warehouseRepo;
        $this->warehouseDatatable = $warehouseDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->warehouseRepository->all();
        return $this->sendResponse($statues, 'Warehouses retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->warehouseRepository->getSummaryData();
        return $this->sendResponse($summary, 'Warehouse summary retrieved successfully.');
    }


    // Get DataTable records
    public function getWarehouseDataTable(Request $request)
    {
        $data = WarehouseDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Warehouse DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Warehouse $state)
    public function show($warehouse)
    {
        $data = $this->warehouseRepository->find($warehouse);
        // check if city exists
        if (!$data) {
            return $this->sendError('Warehouse not found');
        }
        $summary = $this->warehouseRepository->getData($warehouse);
        return $this->sendResponse($summary, 'Warehouse retrieved successfully.');
    }

    public function store(WarehouseRequest $request)
    {
        $warehouse = $this->warehouseRepository->store($request->all());
        return $this->sendResponse($warehouse, 'Warehouse created successfully!');
    }

    // Update country
    public function update(WarehouseRequest $request, $warehouse)
//    public function update(Request $request, Country $country)
    {
        $data = $this->warehouseRepository->find($warehouse);
        // check if city exists
        if (!$data) {
            return $this->sendError('Warehouse not found');
        }
        $this->warehouseRepository->update($data, $request->all());
        return $this->sendResponse($warehouse, 'Warehouse updated successfully!');
    }

    // Delete country
//    public function destroy(Warehouse $state)
    public function destroy($warehouse)
    {
        $data = $this->warehouseRepository->find($warehouse);
        // check if state exists
        if (!$data) {
            return $this->sendError('Warehouse not found');
        }
        $this->warehouseRepository->delete($data);
        return $this->sendSuccess('Warehouse deleted successfully!');
    }
}
