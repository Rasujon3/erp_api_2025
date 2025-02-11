<?php

namespace App\Modules\City\Controllers;

use App\Modules\Admin\Models\Country;
use App\Modules\City\Queries\CityDatatable;
use App\Modules\City\Repositories\CityRepository;
use App\Modules\City\Requests\CityRequest;
use App\Modules\States\Models\State;
use App\Modules\States\Queries\StateDatatable;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\States\Requests\StateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class CityController extends AppBaseController
{
    protected CityRepository $cityRepository;
    protected CityDatatable $cityDatatable;

    public function __construct(CityRepository $cityRepo, CityDatatable $cityDatatable)
    {
        $this->cityRepository = $cityRepo;
        $this->cityDatatable = $cityDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->cityRepository->all();
        return $this->sendResponse($statues, 'Cities retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->cityRepository->getSummaryData();
        return $this->sendResponse($summary, 'City summary retrieved successfully.');
    }


    // Get DataTable records
    public function getCitiesDataTable(Request $request)
    {
        $data = CityDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'City DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(City $state)
    public function show($city)
    {
        $data = $this->cityRepository->find($city);
        // check if city exists
        if (!$data) {
            return $this->sendError('City not found');
        }
        $summary = $this->cityRepository->getData($city);
        return $this->sendResponse($summary, 'City retrieved successfully.');
    }

    public function store(CityRequest $request)
    {
        $city = $this->cityRepository->store($request->all());
        return $this->sendResponse($city, 'City created successfully!');
    }

    // Update country
    public function update(CityRequest $request, $city)
//    public function update(Request $request, Country $country)
    {
        $data = $this->cityRepository->find($city);
        // check if city exists
        if (!$data) {
            return $this->sendError('City not found');
        }
        $this->cityRepository->update($data, $request->all());
        return $this->sendResponse($city, 'City updated successfully!');
    }

    // Delete country
//    public function destroy(City $state)
    public function destroy($state)
    {
        $data = $this->cityRepository->find($state);
        // check if state exists
        if (!$data) {
            return $this->sendError('City not found');
        }
        $this->cityRepository->delete($data);
        return $this->sendSuccess('City deleted successfully!');
    }
}
