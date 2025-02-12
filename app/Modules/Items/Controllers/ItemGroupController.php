<?php

namespace App\Modules\Items\Controllers;

use App\Modules\Items\Queries\ItemGroupDatatable;
use App\Modules\Items\Repositories\ItemGroupRepository;
use App\Modules\Items\Requests\ItemGroupRequest;
use App\Modules\Stores\Queries\StoreDatatable;
use App\Modules\Stores\Repositories\StoreRepository;
use App\Modules\Stores\Requests\StoreRequest;
use App\Modules\TaxRates\Queries\TaxRateDatatable;
use App\Modules\TaxRates\Repositories\TaxRateRepository;
use App\Modules\TaxRates\Requests\TaxRateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class ItemGroupController extends AppBaseController
{
    protected ItemGroupRepository $itemGroupRepository;
    protected ItemGroupDatatable $itemGroupDatatable;

    public function __construct(ItemGroupRepository $itemGroupRepo, ItemGroupDatatable $itemGroupDatatable)
    {
        $this->itemGroupRepository = $itemGroupRepo;
        $this->itemGroupDatatable = $itemGroupDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->itemGroupRepository->all();
        return $this->sendResponse($statues, 'Item Groups retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->itemGroupRepository->getSummaryData();
        return $this->sendResponse($summary, 'Item Group summary retrieved successfully.');
    }


    // Get DataTable records
    public function getItemGroupsDataTable(Request $request)
    {
        $data = ItemGroupDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Item Group DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Item Group $state)
    public function show($itemGroup)
    {
        $data = $this->itemGroupRepository->find($itemGroup);
        // check if city exists
        if (!$data) {
            return $this->sendError('Item Group not found');
        }
//        $summary = $this->storeRepository->getData($itemGroup);
        return $this->sendResponse($data, 'Item Group retrieved successfully.');
    }

    public function store(ItemGroupRequest $request)
    {
        $itemGroup = $this->itemGroupRepository->store($request->all());
        return $this->sendResponse($itemGroup, 'Item Group created successfully!');
    }

    // Update country
    public function update(ItemGroupRequest $request, $itemGroup)
//    public function update(Request $request, Country $country)
    {
        $data = $this->itemGroupRepository->find($itemGroup);
        // check if city exists
        if (!$data) {
            return $this->sendError('Item Group not found');
        }
        $this->itemGroupRepository->update($data, $request->all());
        return $this->sendResponse($itemGroup, 'Item Group updated successfully!');
    }

    // Delete country
//    public function destroy(Item Group $state)
    public function destroy($itemGroup)
    {
        $data = $this->itemGroupRepository->find($itemGroup);
        // check if state exists
        if (!$data) {
            return $this->sendError('Item Group not found');
        }
        $this->itemGroupRepository->delete($data);
        return $this->sendSuccess('Item Group deleted successfully!');
    }
}
