<?php

namespace App\Modules\Colors\Controllers;

use App\Modules\Brands\Queries\BrandDatatable;
use App\Modules\Brands\Repositories\BrandRepository;
use App\Modules\Brands\Requests\BrandRequest;
use App\Modules\Colors\Queries\ColorDatatable;
use App\Modules\Colors\Repositories\ColorRepository;
use App\Modules\Colors\Requests\ColorRequest;
use App\Modules\Groups\Queries\GroupDatatable;
use App\Modules\Groups\Repositories\GroupRepository;
use App\Modules\Groups\Requests\GroupRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class ColorController extends AppBaseController
{
    protected ColorRepository $colorRepository;
    protected ColorDatatable $colorDatatable;

    public function __construct(ColorRepository $colorRepo, ColorDatatable $colorDatatable)
    {
        $this->colorRepository = $colorRepo;
        $this->colorDatatable = $colorDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->colorRepository->all();
        return $this->sendResponse($statues, 'Colors retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->colorRepository->getSummaryData();
        return $this->sendResponse($summary, 'Color summary retrieved successfully.');
    }

    // Get DataTable records
    public function getBrandsDataTable(Request $request)
    {
        $data = ColorDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Color DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Color $state)
    public function show($color)
    {
        $data = $this->colorRepository->find($color);
        // check if city exists
        if (!$data) {
            return $this->sendError('Color not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($color);
        return $this->sendResponse($data, 'Color retrieved successfully.');
    }

    public function store(ColorRequest $request)
    {
        $color = $this->colorRepository->store($request->all());
        return $this->sendResponse($color, 'Color created successfully!');
    }

    // Update country
    public function update(ColorRequest $request, $color)
//    public function update(Request $request, Country $country)
    {
        $data = $this->colorRepository->find($color);
        if (!$data) {
            return $this->sendError('Color not found');
        }
        $this->colorRepository->update($data, $request->all());
        return $this->sendResponse($color, 'Color updated successfully!');
    }

    // Delete country
//    public function destroy(Color $state)
    public function destroy($color)
    {
        $data = $this->colorRepository->find($color);
        if (!$data) {
            return $this->sendError('Color not found');
        }
        $this->colorRepository->delete($data);
        return $this->sendSuccess('Color deleted successfully!');
    }
}
