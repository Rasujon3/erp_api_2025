<?php

namespace App\Modules\Bins\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Bins\Repositories\BinRepository;
use App\Modules\Bins\Requests\BinRequest;

class BinController extends AppBaseController
{
    protected BinRepository $binRepository;

    public function __construct(BinRepository $binRepo)
    {
        $this->binRepository = $binRepo;
    }
    // Fetch all data
    public function index(BinRequest $request)
    {
        $statues = $this->binRepository->all($request);
        return $this->sendResponse($statues, 'Bins retrieved successfully.');
    }

    // Store data
    public function store(BinRequest $request)
    {
        $bin = $this->binRepository->store($request->all());
        if (!$bin) {
            return $this->sendError('Something went wrong!!! [BNS-01]', 500);
        }
        return $this->sendResponse($bin, 'Bin created successfully!');
    }

    // Get single details data
    public function show($bin)
    {
        $data = $this->binRepository->find($bin);
        if (!$data) {
            return $this->sendError('Bin not found');
        }
        // $summary = $this->binRepository->getData($bin);
        // return $this->sendResponse($summary, 'Bin retrieved successfully.');
        return $this->sendResponse($data, 'Bin retrieved successfully.');
    }
    // Update data
    public function update(BinRequest $request, $bin)
    {
        $data = $this->binRepository->find($bin);
        if (!$data) {
            return $this->sendError('Bin not found');
        }
        /*
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->binRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('Bin already used, cannot be deleted', 400);
            }
        }
        */
        $updated = $this->binRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [BNS-02]', 500);
        }
        return $this->sendResponse($bin, 'Bin updated successfully!');
    }
    // bulk update
    public function bulkUpdate(BinRequest $request)
    {
        $bulkUpdate = $this->binRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [BNS-03]', 500);
        }
        return $this->sendResponse([],'Bin Bulk updated successfully!');
    }
}
