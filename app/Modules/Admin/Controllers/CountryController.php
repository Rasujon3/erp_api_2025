<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Modules\Admin\Repositories\CountryRepository;
use App\Modules\Admin\Requests\CountryRequest;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Admin\Queries\CountryDatatable;
use App\Helpers\ActivityLogger;
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

    // Fetch all countries
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

    // Get single country details
    public function show(Country $country)
    {
        return $this->sendResponse($country, 'Country retrieved successfully.');
    }

    // Update country
    public function update(CountryRequest $request, Country $country)
    {
        $this->countryRepository->update($country, $request->all());
        return $this->sendResponse($country, 'Country updated successfully!');
    }

    // Delete country
    public function destroy(Country $country)
    {
        $this->countryRepository->delete($country);
        return $this->sendSuccess('Country deleted successfully!');
    }
}
