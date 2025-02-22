<?php

namespace App\Modules\Countries\Controllers;

use App\Modules\Countries\Models\Country;
use Illuminate\Http\Request;
use App\Modules\Countries\Repositories\CountryRepository;
use App\Modules\Countries\Requests\CountryRequest;
use App\Modules\Countries\Queries\CountryDatatable;
use App\Http\Controllers\AppBaseController;

class CountryController extends AppBaseController
{
    protected $countryRepository;
    protected $countryDatatable;

    public function __construct(CountryRepository $countryRepo, CountryDatatable $countryDatatable)
    {
        $this->countryRepository = $countryRepo;
        $this->countryDatatable = $countryDatatable;
    }
    // Fetch all data
    public function index()
    {
        $countries = $this->countryRepository->all();
        return $this->sendResponse($countries, 'Countries retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->countryRepository->getSummaryData();
        return $this->sendResponse($summary, 'Country summary retrieved successfully.');
    }
    // Get DataTable records
    public function getCountriesDataTable(Request $request)
    {
        $data = CountryDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Country DataTable data retrieved successfully.');
    }

    public function store(CountryRequest $request)
    {
        $country = $this->countryRepository->store($request->all());
        return $this->sendResponse($country, 'Country created successfully!');
    }

    // Get single details
    public function show($country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }
        return $this->sendResponse($data, 'Country retrieved successfully.');
    }

    // Update data
    public function update(CountryRequest $request, $country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }
        $this->countryRepository->update($data, $request->all());
        return $this->sendResponse($country, 'Country updated successfully!');
    }

    // Delete data
    public function destroy($country)
    {
        $data = $this->countryRepository->find($country);
        if (!$data) {
            return $this->sendError('Country not found');
        }
        $this->countryRepository->delete($data);
        return $this->sendSuccess('Country deleted successfully!');
    }
}
