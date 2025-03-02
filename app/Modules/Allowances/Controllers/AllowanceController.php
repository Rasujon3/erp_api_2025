<?php

namespace App\Modules\Allowances\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Loans\Repositories\LoanRepository;
use App\Modules\Loans\Requests\LoanRequest;

class AllowanceController extends AppBaseController
{
    protected LoanRepository $loanRepository;

    public function __construct(LoanRepository $loanRepo)
    {
        $this->loanRepository = $loanRepo;
    }
    // Fetch all data
    public function index(LoanRequest $request)
    {
        $data = $this->loanRepository->all($request);
        return $this->sendResponse($data, 'Loans retrieved successfully.');
    }

    // Store data
    public function store(LoanRequest $request)
    {
        $loan = $this->loanRepository->store($request->all());
        if (!$loan) {
            return $this->sendError('Something went wrong!!! [LS-01]', 500);
        }
        return $this->sendResponse($loan, 'Loan created successfully!');
    }

    // Get single details data
    public function show($loan)
    {
        $data = $this->loanRepository->find($loan);
        if (!$data) {
            return $this->sendError('Loan not found');
        }
        $summary = $this->loanRepository->getData($loan);
        return $this->sendResponse($summary, 'Loan retrieved successfully.');
    }
    // Update data
    public function update(LoanRequest $request, $loan)
    {
        $data = $this->loanRepository->find($loan);
        if (!$data) {
            return $this->sendError('Loan not found');
        }
        /*
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->loanRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('Loan already used, cannot be deleted', 400);
            }
        }
        */
        $updated = $this->loanRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [CU-04]', 500);
        }
        return $this->sendResponse($loan, 'Loan updated successfully!');
    }
    // bulk update
    public function bulkUpdate(LoanRequest $request)
    {
        $bulkUpdate = $this->loanRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [CBU-05]', 500);
        }
        return $this->sendResponse([],'Loan Bulk updated successfully!');
    }
}
