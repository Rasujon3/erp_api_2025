<?php

namespace App\Modules\Areas\Controllers;

use App\Modules\Admin\Models\Country;
use App\Modules\Areas\Queries\AreaDatatable;
use App\Modules\Areas\Repositories\AreaRepository;
use App\Modules\Areas\Requests\AreaRequest;
use App\Modules\City\Queries\CityDatatable;
use App\Modules\City\Repositories\CityRepository;
use App\Modules\City\Requests\CityRequest;
use App\Modules\States\Models\State;
use App\Modules\States\Queries\StateDatatable;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\States\Requests\StateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class AreaController extends AppBaseController
{
    protected AreaRepository $areaRepository;
    protected AreaDatatable $areaDatatable;

    public function __construct(AreaRepository $areaRepo, AreaDatatable $areaDatatable)
    {
        $this->areaRepository = $areaRepo;
        $this->areaDatatable = $areaDatatable;
    }

    // Fetch all states
    public function index()
    {
        $areas = $this->areaRepository->all();
        return $this->sendResponse($areas, 'Areas retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->areaRepository->getSummaryData();
        return $this->sendResponse($summary, 'Area summary retrieved successfully.');
    }


    // Get DataTable records
    public function getAreasDataTable(Request $request)
    {
        $data = AreaDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Area DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Area $state)
    public function show($area)
    {
        $data = $this->areaRepository->find($area);
        // check if city exists
        if (!$data) {
            return $this->sendError('Area not found');
        }
        $summary = $this->areaRepository->getData($area);
        return $this->sendResponse($summary, 'Area retrieved successfully.');
    }

    public function store(AreaRequest $request)
    {
        $city = $this->areaRepository->store($request->all());
        return $this->sendResponse($city, 'Area created successfully!');
    }

    // Update country
    public function update(AreaRequest $request, $area)
    {
        $data = $this->areaRepository->find($area);
        // check if city exists
        if (!$data) {
            return $this->sendError('Area not found');
        }
        $this->areaRepository->update($data, $request->all());
        return $this->sendResponse($area, 'Area updated successfully!');
    }

    // Delete country
//    public function destroy(Area $state)
    public function destroy($area)
    {
        $data = $this->areaRepository->find($area);
        // check if state exists
        if (!$data) {
            return $this->sendError('Area not found');
        }
        $this->areaRepository->delete($data);
        return $this->sendSuccess('Area deleted successfully!');
    }
}
