<?php

namespace App\Modules\Sample\Controllers;

use App\Modules\Items\Queries\ItemGroupDatatable;
use App\Modules\Items\Repositories\ItemGroupRepository;
use App\Modules\Items\Requests\ItemGroupRequest;
use App\Modules\Sample\Queries\SampleReceiveDatatable;
use App\Modules\Sample\Repositories\SampleReceiveRepository;
use App\Modules\Sample\Requests\SampleReceiveRequest;
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

class SampleReceiveController extends AppBaseController
{
    protected SampleReceiveRepository $sampleReceiveRepository;
    protected SampleReceiveDatatable $sampleReceiveDatatable;

    public function __construct(SampleReceiveRepository $sampleReceiveRepo, SampleReceiveDatatable $sampleReceiveDatatable)
    {
        $this->sampleReceiveRepository = $sampleReceiveRepo;
        $this->sampleReceiveDatatable = $sampleReceiveDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->sampleReceiveRepository->all();
        return $this->sendResponse($statues, 'Sample Receive retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->sampleReceiveRepository->getSummaryData();
        return $this->sendResponse($summary, 'Sample Receive summary retrieved successfully.');
    }


    // Get DataTable records
    public function getSampleReceivesDataTable(Request $request)
    {
        $data = SampleReceiveDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Sample Receive DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Sample Receive $state)
    public function show($receive)
    {
        $data = $this->sampleReceiveRepository->find($receive);
        // check if city exists
        if (!$data) {
            return $this->sendError('Sample Receive not found');
        }
        $summary = $this->sampleReceiveRepository->getData($receive);
        return $this->sendResponse($summary, 'Sample Receive retrieved successfully.');
    }

    public function store(SampleReceiveRequest $request)
    {
        $receive = $this->sampleReceiveRepository->store($request->all());
        return $this->sendResponse($receive, 'Sample Receive created successfully!');
    }

    // Update country
    public function update(SampleReceiveRequest $request, $receive)
//    public function update(Request $request, Country $country)
    {
        $data = $this->sampleReceiveRepository->find($receive);
        // check if city exists
        if (!$data) {
            return $this->sendError('Sample Receive not found');
        }
        $this->sampleReceiveRepository->update($data, $request->all());
        return $this->sendResponse($receive, 'Sample Receive updated successfully!');
    }

    // Delete country
//    public function destroy(Sample Receive $state)
    public function destroy($receive)
    {
        $data = $this->sampleReceiveRepository->find($receive);

        if (!$data) {
            return $this->sendError('Sample Receive not found');
        }

        // Attempt to delete
        $this->sampleReceiveRepository->delete($data);

        return $this->sendSuccess('Sample Receive deleted successfully!');
    }
}
