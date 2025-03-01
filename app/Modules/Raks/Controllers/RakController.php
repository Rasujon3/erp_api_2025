<?php

namespace App\Modules\Raks\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Raks\Repositories\RakRepository;
use App\Modules\Raks\Requests\RakRequest;

class RakController extends AppBaseController
{
    protected RakRepository $rakRepository;

    public function __construct(RakRepository $rakRepo)
    {
        $this->rakRepository = $rakRepo;
    }
    // Fetch all data
    public function index(RakRequest $request)
    {
        $data = $this->rakRepository->all($request);
        return $this->sendResponse($data, 'Raks retrieved successfully.');
    }

    // Store data
    public function store(RakRequest $request)
    {
        $rak = $this->rakRepository->store($request->all());
        if (!$rak) {
            return $this->sendError('Something went wrong!!! [BNS-01]', 500);
        }
        return $this->sendResponse($rak, 'Rak created successfully!');
    }

    // Get single details data
    public function show($rak)
    {
        $data = $this->rakRepository->find($rak);
        if (!$data) {
            return $this->sendError('Rak not found');
        }
        // $summary = $this->rakRepository->getData($rak);
        // return $this->sendResponse($summary, 'Rak retrieved successfully.');
        return $this->sendResponse($data, 'Rak retrieved successfully.');
    }
    // Update data
    public function update(RakRequest $request, $rak)
    {
        $data = $this->rakRepository->find($rak);
        if (!$data) {
            return $this->sendError('Rak not found');
        }
        /*
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->rakRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('Rak already used, cannot be deleted', 400);
            }
        }
        */
        $updated = $this->rakRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [BNS-02]', 500);
        }
        return $this->sendResponse($rak, 'Rak updated successfully!');
    }
    // bulk update
    public function bulkUpdate(RakRequest $request)
    {
        $bulkUpdate = $this->rakRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [BNS-03]', 500);
        }
        return $this->sendResponse([],'Rak Bulk updated successfully!');
    }
}
