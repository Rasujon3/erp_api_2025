<?php

namespace App\Modules\Allowances\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Allowances\Repositories\AllowanceRepository;
use App\Modules\Allowances\Requests\AllowanceRequest;
use App\Modules\Loans\Repositories\LoanRepository;
use App\Modules\Loans\Requests\LoanRequest;

class AllowanceController extends AppBaseController
{
    protected AllowanceRepository $allowanceRepository;

    public function __construct(AllowanceRepository $allowanceRepo)
    {
        $this->allowanceRepository = $allowanceRepo;
    }
    // Fetch all data
    public function index(AllowanceRequest $request)
    {
        $data = $this->allowanceRepository->all($request);
        return $this->sendResponse($data, 'Allowances retrieved successfully.');
    }

    // Store data
    public function store(AllowanceRequest $request)
    {
        $allowance = $this->allowanceRepository->store($request->all());
        if (!$allowance) {
            return $this->sendError('Something went wrong!!! [ALU-01]', 500);
        }
        return $this->sendResponse($allowance, 'Allowance created successfully!');
    }

    // Get single details data
    public function show($allowance)
    {
        $data = $this->allowanceRepository->find($allowance);
        if (!$data) {
            return $this->sendError('Allowance not found');
        }
        $summary = $this->allowanceRepository->getData($allowance);
        return $this->sendResponse($summary, 'Allowance retrieved successfully.');
    }
    // Update data
    public function update(AllowanceRequest $request, $allowance)
    {
        $data = $this->allowanceRepository->find($allowance);
        if (!$data) {
            return $this->sendError('Allowance not found');
        }
        /*
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->allowanceRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('Allowance already used, cannot be deleted', 400);
            }
        }
        */
        $updated = $this->allowanceRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [ALU-02]', 500);
        }
        return $this->sendResponse($allowance, 'Allowance updated successfully!');
    }
    // bulk update
    public function bulkUpdate(AllowanceRequest $request)
    {
        $bulkUpdate = $this->allowanceRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [ALU-03]', 500);
        }
        return $this->sendResponse([],'Allowance Bulk updated successfully!');
    }
}
