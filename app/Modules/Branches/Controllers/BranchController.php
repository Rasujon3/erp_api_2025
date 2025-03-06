<?php

namespace App\Modules\Branches\Controllers;

use App\Modules\Branches\Queries\BranchDatatable;
use App\Modules\Branches\Repositories\BranchRepository;
use App\Modules\Branches\Requests\BranchRequest;
use App\Http\Controllers\AppBaseController;

class BranchController extends AppBaseController
{
    protected BranchRepository $branchRepository;

    public function __construct(BranchRepository $areaRepo)
    {
        $this->branchRepository = $areaRepo;
    }
    // Fetch all data
    public function index(BranchRequest $request)
    {
        $banks = $this->branchRepository->all($request);
        return $this->sendResponse($banks, 'Branches retrieved successfully.');
    }
    // Get single details
    public function show($branch)
    {
        $data = $this->branchRepository->find($branch);
        if (!$data) {
            return $this->sendError('Branch not found');
        }
        return $this->sendResponse($data, 'Branch retrieved successfully.');
    }
    // store data
    public function store(BranchRequest $request)
    {
        $branch = $this->branchRepository->store($request->all());
        if (!$branch) {
            return $this->sendError('Something went wrong!!! [BCS-01]', 500);
        }
        return $this->sendResponse($branch, 'Branch created successfully!');
    }
    // Update data
    public function update(BranchRequest $request, $branch)
    {
        $data = $this->branchRepository->find($branch);
        if (!$data) {
            return $this->sendError('Branch not found');
        }
        $updated = $this->branchRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [BCU-02]', 500);
        }
        return $this->sendResponse($branch, 'Branch updated successfully!');
    }
    // bulk update
    public function bulkUpdate(BranchRequest $request)
    {
        $bulkUpdate = $this->branchRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [BCBU-03]', 500);
        }
        return $this->sendResponse([],'Branch Bulk updated successfully!');
    }
}
