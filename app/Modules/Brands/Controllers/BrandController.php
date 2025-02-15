<?php

namespace App\Modules\Brands\Controllers;

use App\Modules\Brands\Queries\BrandDatatable;
use App\Modules\Brands\Repositories\BrandRepository;
use App\Modules\Brands\Requests\BrandRequest;
use App\Modules\Groups\Queries\GroupDatatable;
use App\Modules\Groups\Repositories\GroupRepository;
use App\Modules\Groups\Requests\GroupRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class BrandController extends AppBaseController
{
    protected BrandRepository $brandRepository;
    protected BrandDatatable $brandDatatable;

    public function __construct(BrandRepository $brandRepo, BrandDatatable $brandDatatable)
    {
        $this->brandRepository = $brandRepo;
        $this->brandDatatable = $brandDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->brandRepository->all();
        return $this->sendResponse($statues, 'Brands retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->brandRepository->getSummaryData();
        return $this->sendResponse($summary, 'Brand summary retrieved successfully.');
    }


    // Get DataTable records
    public function getBrandsDataTable(Request $request)
    {
        $data = BrandDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Brand DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Brand $state)
    public function show($brand)
    {
        $data = $this->brandRepository->find($brand);
        // check if city exists
        if (!$data) {
            return $this->sendError('Brand not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($brand);
        return $this->sendResponse($data, 'Brand retrieved successfully.');
    }

    public function store(BrandRequest $request)
    {
        $brand = $this->brandRepository->store($request->all());
        return $this->sendResponse($brand, 'Brand created successfully!');
    }

    // Update country
    public function update(BrandRequest $request, $brand)
//    public function update(Request $request, Country $country)
    {
        $data = $this->brandRepository->find($brand);
        if (!$data) {
            return $this->sendError('Brand not found');
        }
        $this->brandRepository->update($data, $request->all());
        return $this->sendResponse($brand, 'Brand updated successfully!');
    }

    // Delete country
//    public function destroy(Brand $state)
    public function destroy($brand)
    {
        $data = $this->brandRepository->find($brand);
        if (!$data) {
            return $this->sendError('Brand not found');
        }
        $this->brandRepository->delete($data);
        return $this->sendSuccess('Brand deleted successfully!');
    }
}
