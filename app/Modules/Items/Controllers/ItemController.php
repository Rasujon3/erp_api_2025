<?php

namespace App\Modules\Items\Controllers;

use App\Modules\Items\Queries\ItemDatatable;
use App\Modules\Items\Repositories\ItemRepository;
use App\Modules\Items\Requests\ItemRequest;
use App\Modules\Stores\Queries\StoreDatatable;
use App\Modules\Stores\Repositories\StoreRepository;
use App\Modules\Stores\Requests\StoreRequest;
use App\Modules\TaxRates\Queries\TaxRateDatatable;
use App\Modules\TaxRates\Repositories\TaxRateRepository;
use App\Modules\TaxRates\Requests\TaxRateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class ItemController extends AppBaseController
{
    protected ItemRepository $itemRepository;
    protected ItemDatatable $itemDatatable;

    public function __construct(ItemRepository $itemRepo, ItemDatatable $itemDatatable)
    {
        $this->itemRepository = $itemRepo;
        $this->itemDatatable = $itemDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->itemRepository->all();
        return $this->sendResponse($statues, 'Items retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->itemRepository->getSummaryData();
        return $this->sendResponse($summary, 'Item summary retrieved successfully.');
    }


    // Get DataTable records
    public function getItemsDataTable(Request $request)
    {
        $data = ItemDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Item DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Item $state)
    public function show($item)
    {
        $data = $this->itemRepository->find($item);
        // check if city exists
        if (!$data) {
            return $this->sendError('Item not found');
        }
        $summary = $this->itemRepository->getData($item);
        return $this->sendResponse($summary, 'Item retrieved successfully.');
    }

    public function store(ItemRequest $request)
    {
        $item = $this->itemRepository->store($request->all());
        return $this->sendResponse($item, 'Item created successfully!');
    }

    // Update country
    public function update(ItemRequest $request, $item)
//    public function update(Request $request, Country $country)
    {
        $data = $this->itemRepository->find($item);
        // check if city exists
        if (!$data) {
            return $this->sendError('Item not found');
        }
        $this->itemRepository->update($data, $request->all());
        return $this->sendResponse($item, 'Item updated successfully!');
    }

    // Delete country
//    public function destroy(Item $state)
    public function destroy($item)
    {
        $data = $this->itemRepository->find($item);
        // check if state exists
        if (!$data) {
            return $this->sendError('Item not found');
        }
        $this->itemRepository->delete($data);
        return $this->sendSuccess('Item deleted successfully!');
    }
}
