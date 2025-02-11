<?php

namespace App\Modules\Currencies\Controllers;

use App\Modules\Admin\Models\Country;
use App\Modules\Areas\Queries\AreaDatatable;
use App\Modules\Areas\Repositories\AreaRepository;
use App\Modules\Areas\Requests\AreaRequest;
use App\Modules\City\Queries\CityDatatable;
use App\Modules\City\Repositories\CityRepository;
use App\Modules\City\Requests\CityRequest;
use App\Modules\Currencies\Queries\CurrencyDatatable;
use App\Modules\Currencies\Repositories\CurrencyRepository;
use App\Modules\Currencies\Requests\CurrencyRequest;
use App\Modules\States\Models\State;
use App\Modules\States\Queries\StateDatatable;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\States\Requests\StateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class CurrencyController extends AppBaseController
{
    protected CurrencyRepository $currencyRepository;
    protected CurrencyDatatable $currencyDatatable;

    public function __construct(CurrencyRepository $currencyRepo, CurrencyDatatable $currencyDatatable)
    {
        $this->currencyRepository = $currencyRepo;
        $this->currencyDatatable = $currencyDatatable;
    }

    // Fetch all states
    public function index()
    {
        $areas = $this->currencyRepository->all();
        return $this->sendResponse($areas, 'Currencies retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->currencyRepository->getSummaryData();
        return $this->sendResponse($summary, 'Currency summary retrieved successfully.');
    }


    // Get DataTable records
    public function getCurrenciesDataTable(Request $request)
    {
        $data = CurrencyDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Currency DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Currency $state)
    public function show($currency)
    {
        $data = $this->currencyRepository->find($currency);
        // check if currency exists
        if (!$data) {
            return $this->sendError('Currency not found');
        }
//        $summary = $this->currencyRepository->getData($currency);
        return $this->sendResponse($data, 'Currency retrieved successfully.');
    }

    public function store(CurrencyRequest $request)
    {
        $city = $this->currencyRepository->store($request->all());
        return $this->sendResponse($city, 'Currency created successfully!');
    }

    // Update country
    public function update(CurrencyRequest $request, $currency)
    {
        $data = $this->currencyRepository->find($currency);
        // check if city exists
        if (!$data) {
            return $this->sendError('Currency not found');
        }
        $this->currencyRepository->update($data, $request->all());
        return $this->sendResponse($currency, 'Currency updated successfully!');
    }

    // Delete country
//    public function destroy(Currency $state)
    public function destroy($currency)
    {
        $data = $this->currencyRepository->find($currency);
        // check if state exists
        if (!$data) {
            return $this->sendError('Currency not found');
        }
        $this->currencyRepository->delete($data);
        return $this->sendSuccess('Currency deleted successfully!');
    }
}
