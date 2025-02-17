<?php

namespace App\Modules\Units\Controllers;

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
use App\Modules\Units\Queries\UnitDatatable;
use App\Modules\Units\Repositories\UnitRepository;
use App\Modules\Units\Requests\UnitRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class UnitController extends AppBaseController
{
    protected UnitRepository $unitRepository;
    protected UnitDatatable $unitDatatable;

    public function __construct(UnitRepository $unitRepo, UnitDatatable $unitDatatable)
    {
        $this->unitRepository = $unitRepo;
        $this->unitDatatable = $unitDatatable;
    }

    // Fetch all states
    public function index()
    {
        $sizes = $this->unitRepository->all();
        return $this->sendResponse($sizes, 'Units retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->unitRepository->getSummaryData();
        return $this->sendResponse($summary, 'Unit summary retrieved successfully.');
    }

    // Get DataTable records
    public function getSizesDataTable(Request $request)
    {
        $data = UnitDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Unit DataTable data retrieved successfully.');
    }

    // Get single country details
    public function show($unit)
    {
        $data = $this->unitRepository->find($unit);
        if (!$data) {
            return $this->sendError('Unit not found');
        }
        return $this->sendResponse($data, 'Unit retrieved successfully.');
    }

    public function store(UnitRequest $request)
    {
        $unit = $this->unitRepository->store($request->all());
        if (!$unit) {
            return $this->sendError('Something went wrong', 400);
        }
        return $this->sendResponse($unit, 'Unit created successfully!');
    }

    // Update country
    public function update(UnitRequest $request, $unit)
    {
        $data = $this->unitRepository->find($unit);
        if (!$data) {
            return $this->sendError('Unit not found');
        }
        $this->unitRepository->update($data, $request->all());
        return $this->sendResponse($unit, 'Unit updated successfully!');
    }

    // Delete country
    public function destroy($unit)
    {
        $data = $this->unitRepository->find($unit);
        if (!$data) {
            return $this->sendError('Unit not found');
        }
        $this->unitRepository->delete($data);
        return $this->sendSuccess('Unit deleted successfully!');
    }
}
