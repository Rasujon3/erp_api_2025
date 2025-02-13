<?php

namespace App\Modules\TaxRates\Controllers;

use App\Modules\Stores\Queries\StoreDatatable;
use App\Modules\Stores\Repositories\StoreRepository;
use App\Modules\Stores\Requests\StoreRequest;
use App\Modules\TaxRates\Queries\TaxRateDatatable;
use App\Modules\TaxRates\Repositories\TaxRateRepository;
use App\Modules\TaxRates\Requests\TaxRateRequest;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;

class TaxRateController extends AppBaseController
{
    protected TaxRateRepository $taxRateRepository;
    protected TaxRateDatatable $taxRateDatatable;

    public function __construct(TaxRateRepository $taxRateRepo, TaxRateDatatable $taxRateDatatable)
    {
        $this->taxRateRepository = $taxRateRepo;
        $this->taxRateDatatable = $taxRateDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->taxRateRepository->all();
        return $this->sendResponse($statues, 'Tax Rates retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->taxRateRepository->getSummaryData();
        return $this->sendResponse($summary, 'Tax Rate summary retrieved successfully.');
    }


    // Get DataTable records
    public function getTaxRatesDataTable(Request $request)
    {
        $data = TaxRateDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Tax Rate DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Tax Rate $state)
    public function show($taxRate)
    {
        $data = $this->taxRateRepository->find($taxRate);
        // check if city exists
        if (!$data) {
            return $this->sendError('Tax Rate not found');
        }
//        $summary = $this->storeRepository->getData($taxRate);
        return $this->sendResponse($data, 'Tax Rate retrieved successfully.');
    }

    public function store(TaxRateRequest $request)
    {
        $taxRate = $this->taxRateRepository->store($request->all());
        return $this->sendResponse($taxRate, 'Tax Rate created successfully!');
    }

    // Update country
    public function update(TaxRateRequest $request, $taxRate)
//    public function update(Request $request, Country $country)
    {
        $data = $this->taxRateRepository->find($taxRate);
        // check if city exists
        if (!$data) {
            return $this->sendError('Tax Rate not found');
        }
        $this->taxRateRepository->update($data, $request->all());
        return $this->sendResponse($taxRate, 'Tax Rate updated successfully!');
    }

    // Delete country
//    public function destroy(Tax Rate $state)
    public function destroy($taxRate)
    {
        try {
            $data = $this->taxRateRepository->find($taxRate);

            if (!$data) {
                return $this->sendError('Tax Rate not found');
            }

            // Attempt to delete
            $this->taxRateRepository->delete($data);

            return $this->sendSuccess('Tax Rate deleted successfully!');
        } catch (Exception $e) {
            Log::error('Tax Rate Deletion Error: ' . $e->getMessage());

            // Return error response
            return $this->sendError($e->getMessage(), 400);
        }
    }

}
