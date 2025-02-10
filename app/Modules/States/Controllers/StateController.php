<?php

namespace App\Modules\States\Controllers;

use App\Modules\Admin\Models\Country;
use App\Modules\States\Models\State;
use App\Modules\States\Queries\StateDatatable;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\States\Requests\StateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class StateController extends AppBaseController
{
    protected $stateRepository;
    protected $stateDatatable;

    public function __construct(StateRepository $stateRepo, StateDatatable $stateDatatable)
    {
        $this->stateRepository = $stateRepo;
        $this->stateDatatable = $stateDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->stateRepository->all();
        return $this->sendResponse($statues, 'States retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->stateRepository->getSummaryData();
        return $this->sendResponse($summary, 'State summary retrieved successfully.');
    }


    // Get DataTable records
    public function getStatesDataTable(Request $request)
    {
        $data = StateDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'State DataTable data retrieved successfully.');
    }


    public function store(StateRequest $request)
    {
        $state = $this->stateRepository->store($request->all());
        return $this->sendResponse($state, 'State created successfully!');
    }

    // Get single country details
//    public function show(State $state)
    public function show($state)
    {
        $data = $this->stateRepository->find($state);
        // check if state exists
        if (!$data) {
            return $this->sendError('State not found');
        }
        $summary = $this->stateRepository->getData($state);
        return $this->sendResponse($summary, 'State retrieved successfully.');
//        return $this->sendResponse($state, 'State retrieved successfully.');
    }

    // Update country
    public function update(StateRequest $request, $state)
//    public function update(Request $request, Country $country)
    {
        $data = $this->stateRepository->find($state);
        // check if state exists
        if (!$data) {
            return $this->sendError('State not found');
        }
        $this->stateRepository->update($data, $request->all());
        return $this->sendResponse($state, 'State updated successfully!');
    }

    // Delete country
//    public function destroy(State $state)
    public function destroy($state)
    {
        $data = $this->stateRepository->find($state);
        // check if state exists
        if (!$data) {
            return $this->sendError('State not found');
        }
        $this->stateRepository->delete($data);
        return $this->sendSuccess('State deleted successfully!');
    }
}
