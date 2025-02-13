<?php

namespace App\Modules\ProductUnits\Controllers;

use App\Modules\ProductUnits\Queries\ProductUnitDatatable;
use App\Modules\ProductUnits\Repositories\ProductUnitRepository;
use App\Modules\ProductUnits\Requests\ProductUnitRequest;
use App\Modules\Sample\Queries\SampleCategoryDatatable;
use App\Modules\Sample\Repositories\SampleCategoryRepository;
use App\Modules\Sample\Requests\SampleCategoryRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class ProductUnitController extends AppBaseController
{
    protected ProductUnitRepository $productUnitRepository;
    protected ProductUnitDatatable $productUnitDatatable;

    public function __construct(ProductUnitRepository $productUnitRepo, ProductUnitDatatable $productUnitDatatable)
    {
        $this->productUnitRepository = $productUnitRepo;
        $this->productUnitDatatable = $productUnitDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->productUnitRepository->all();
        return $this->sendResponse($statues, 'Product Units retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->productUnitRepository->getSummaryData();
        return $this->sendResponse($summary, 'Product Unit summary retrieved successfully.');
    }


    // Get DataTable records
    public function getProductUnitsDataTable(Request $request)
    {
        $data = ProductUnitDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Product Unit DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Product Unit $state)
    public function show($category)
    {
        $data = $this->productUnitRepository->find($category);
        // check if city exists
        if (!$data) {
            return $this->sendError('Product Unit not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($category);
        return $this->sendResponse($data, 'Product Unit retrieved successfully.');
    }

    public function store(ProductUnitRequest $request)
    {
        $category = $this->productUnitRepository->store($request->all());
        return $this->sendResponse($category, 'Product Unit created successfully!');
    }

    // Update country
    public function update(ProductUnitRequest $request, $category)
//    public function update(Request $request, Country $country)
    {
        $data = $this->productUnitRepository->find($category);
        // check if city exists
        if (!$data) {
            return $this->sendError('Product Unit not found');
        }
        $this->productUnitRepository->update($data, $request->all());
        return $this->sendResponse($category, 'Product Unit updated successfully!');
    }

    // Delete country
//    public function destroy(Product Unit $state)
    public function destroy($category)
    {
        $data = $this->productUnitRepository->find($category);
        // check if state exists
        if (!$data) {
            return $this->sendError('Product Unit not found');
        }
        $this->productUnitRepository->delete($data);
        return $this->sendSuccess('Product Unit deleted successfully!');
    }
}
