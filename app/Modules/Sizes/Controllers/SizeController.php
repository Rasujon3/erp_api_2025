<?php

namespace App\Modules\Sizes\Controllers;

use App\Modules\Brands\Queries\BrandDatatable;
use App\Modules\Brands\Repositories\BrandRepository;
use App\Modules\Brands\Requests\BrandRequest;
use App\Modules\Colors\Queries\ColorDatatable;
use App\Modules\Colors\Repositories\ColorRepository;
use App\Modules\Colors\Requests\ColorRequest;
use App\Modules\Groups\Queries\GroupDatatable;
use App\Modules\Groups\Repositories\GroupRepository;
use App\Modules\Groups\Requests\GroupRequest;
use App\Modules\Sizes\Queries\SizeDatatable;
use App\Modules\Sizes\Repositories\SizeRepository;
use App\Modules\Sizes\Requests\SizeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class SizeController extends AppBaseController
{
    protected SizeRepository $sizeRepository;
    protected SizeDatatable $sizeDatatable;

    public function __construct(SizeRepository $sizeRepo, SizeDatatable $sizeDatatable)
    {
        $this->sizeRepository = $sizeRepo;
        $this->sizeDatatable = $sizeDatatable;
    }

    // Fetch all states
    public function index()
    {
        $sizes = $this->sizeRepository->all();
        return $this->sendResponse($sizes, 'Sizes retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->sizeRepository->getSummaryData();
        return $this->sendResponse($summary, 'Size summary retrieved successfully.');
    }

    // Get DataTable records
    public function getSizesDataTable(Request $request)
    {
        $data = SizeDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Size DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Size $state)
    public function show($size)
    {
        $data = $this->sizeRepository->find($size);
        // check if city exists
        if (!$data) {
            return $this->sendError('Size not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($size);
        return $this->sendResponse($data, 'Size retrieved successfully.');
    }

    public function store(SizeRequest $request)
    {
        $size = $this->sizeRepository->store($request->all());
        return $this->sendResponse($size, 'Size created successfully!');
    }

    // Update country
    public function update(SizeRequest $request, $size)
//    public function update(Request $request, Country $country)
    {
        $data = $this->sizeRepository->find($size);
        if (!$data) {
            return $this->sendError('Size not found');
        }
        $this->sizeRepository->update($data, $request->all());
        return $this->sendResponse($size, 'Size updated successfully!');
    }

    // Delete country
//    public function destroy(Size $state)
    public function destroy($size)
    {
        $data = $this->sizeRepository->find($size);
        if (!$data) {
            return $this->sendError('Size not found');
        }
        $this->sizeRepository->delete($data);
        return $this->sendSuccess('Size deleted successfully!');
    }
}
