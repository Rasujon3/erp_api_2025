<?php

namespace App\Modules\Banks\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Banks\Repositories\BankRepository;
use App\Modules\Banks\Requests\BankRequest;

class BankController extends AppBaseController
{
    protected BankRepository $bankRepository;
    public function __construct(BankRepository $bankRepo)
    {
        $this->bankRepository = $bankRepo;
    }
    // Fetch all data
    public function index(BankRequest $request)
    {
        $banks = $this->bankRepository->all($request);
        return $this->sendResponse($banks, 'Banks retrieved successfully.');
    }
    // Get single details
    public function show($bank)
    {
        $data = $this->bankRepository->find($bank);
        if (!$data) {
            return $this->sendError('Bank not found');
        }
        return $this->sendResponse($data, 'Bank retrieved successfully.');
    }
    // store data
    public function store(BankRequest $request)
    {
        $bank = $this->bankRepository->store($request->all());
        if (!$bank) {
            return $this->sendError('Something went wrong!!! [BC-01]', 500);
        }
        return $this->sendResponse($bank, 'Bank created successfully!');
    }
    // Update data
    public function update(BankRequest $request, $bank)
    {
        $data = $this->bankRepository->find($bank);
        if (!$data) {
            return $this->sendError('Bank not found');
        }
        $updated = $this->bankRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [BC-02]', 500);
        }
        return $this->sendResponse($bank, 'Bank updated successfully!');
    }
    // bulk update
    public function bulkUpdate(BankRequest $request)
    {
        $bulkUpdate = $this->bankRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [BC-03]', 500);
        }
        return $this->sendResponse([],'Bank Bulk updated successfully!');
    }
}
