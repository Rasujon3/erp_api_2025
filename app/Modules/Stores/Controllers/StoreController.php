<?php

namespace App\Modules\Stores\Controllers;

use App\Modules\Stores\Queries\StoreDatatable;
use App\Modules\Stores\Repositories\StoreRepository;
use App\Modules\Stores\Requests\StoreRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class StoreController extends AppBaseController
{
    protected StoreRepository $storeRepository;
    protected StoreDatatable $storeDatatable;

    public function __construct(StoreRepository $storeRepo, StoreDatatable $storeDatatable)
    {
        $this->storeRepository = $storeRepo;
        $this->storeDatatable = $storeDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->storeRepository->all();
        return $this->sendResponse($statues, 'Stores retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->storeRepository->getSummaryData();
        return $this->sendResponse($summary, 'Store summary retrieved successfully.');
    }


    // Get DataTable records
    public function getStoresDataTable(Request $request)
    {
        $data = StoreDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Store DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Store $state)
    public function show($store)
    {
        $data = $this->storeRepository->find($store);
        // check if city exists
        if (!$data) {
            return $this->sendError('Store not found');
        }
//        $summary = $this->storeRepository->getData($store);
        return $this->sendResponse($data, 'Store retrieved successfully.');
    }

    public function store(StoreRequest $request)
    {
        $store = $this->storeRepository->store($request->all());
        return $this->sendResponse($store, 'Store created successfully!');
    }

    // Update country
    public function update(StoreRequest $request, $store)
//    public function update(Request $request, Country $country)
    {
        $data = $this->storeRepository->find($store);
        // check if city exists
        if (!$data) {
            return $this->sendError('Store not found');
        }
        $this->storeRepository->update($data, $request->all());
        return $this->sendResponse($store, 'Store updated successfully!');
    }

    // Delete country
//    public function destroy(Store $state)
    public function destroy($store)
    {
        $data = $this->storeRepository->find($store);
        // check if state exists
        if (!$data) {
            return $this->sendError('Store not found');
        }
        $this->storeRepository->delete($data);
        return $this->sendSuccess('Store deleted successfully!');
    }
}
