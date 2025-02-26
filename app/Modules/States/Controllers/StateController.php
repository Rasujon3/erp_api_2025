<?php

namespace App\Modules\States\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\States\Queries\StateDatatable;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\States\Requests\StateBulkRequest;
use App\Modules\States\Requests\StateRequest;

class StateController extends AppBaseController
{
    protected $stateRepository;
    protected $stateDatatable;

    public function __construct(StateRepository $stateRepo, StateDatatable $stateDatatable)
    {
        $this->stateRepository = $stateRepo;
        $this->stateDatatable = $stateDatatable;
    }

    // Fetch all data
    public function index()
    {
        $statues = $this->stateRepository->all();
        return $this->sendResponse($statues, 'States retrieved successfully.');
    }

    // Store data
    public function store(StateRequest $request)
    {
        $state = $this->stateRepository->store($request->all());
        if (!$state) {
            return $this->sendError('Something went wrong!!! [SCS-01]', 500);
        }
        return $this->sendResponse($state, 'State created successfully!');
    }

    // Get single details data
    public function show($state)
    {
        $data = $this->stateRepository->find($state);
        if (!$data) {
            return $this->sendError('State not found');
        }
        $summary = $this->stateRepository->getData($state);
        return $this->sendResponse($summary, 'State retrieved successfully.');
    }
    // Update data
    public function update(StateRequest $request, $state)
    {
        $data = $this->stateRepository->find($state);
        if (!$data) {
            return $this->sendError('State not found');
        }
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->stateRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('State already used, cannot be deleted', 400);
            }
        }
        $updated = $this->stateRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [SCU-01]', 500);
        }
        return $this->sendResponse($state, 'State updated successfully!');
    }
    // bulk update
    public function bulkUpdate(StateBulkRequest $request)
    {
        $bulkUpdate = $this->stateRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [SCBU-05]', 500);
        }
        return $this->sendResponse([],'State Bulk updated successfully!');
    }
}
