<?php

namespace App\Modules\Leaves\Controllers;

use App\Modules\Departments\Queries\DepartmentDatatable;
use App\Modules\Departments\Repositories\DepartmentRepository;
use App\Modules\Departments\Requests\DepartmentRequest;
use App\Modules\Leaves\Queries\LeaveDatatable;
use App\Modules\Leaves\Repositories\LeaveRepository;
use App\Modules\Leaves\Requests\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class LeaveController extends AppBaseController
{
    protected LeaveRepository $leaveRepository;
    protected LeaveDatatable $leaveDatatable;

    public function __construct(LeaveRepository $leaveRepo, LeaveDatatable $leaveDatatable)
    {
        $this->leaveRepository = $leaveRepo;
        $this->leaveDatatable = $leaveDatatable;
    }

    // Fetch all data
    public function index()
    {
        $leaves = $this->leaveRepository->all();
        return $this->sendResponse($leaves, 'Leaves retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->leaveRepository->getSummaryData();
        return $this->sendResponse($summary, 'Leave summary retrieved successfully.');
    }

    // Get DataTable records
    public function getDepartmentsDataTable(Request $request)
    {
        $data = LeaveDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Leave DataTable data retrieved successfully.');
    }

    // Get single country details
    public function show($leave)
    {
        $data = $this->leaveRepository->find($leave);
        if (!$data) {
            return $this->sendError('Leave not found');
        }
        return $this->sendResponse($data, 'Leave retrieved successfully.');
    }

    public function store(LeaveRequest $request)
    {
        $leave = $this->leaveRepository->store($request->all());
        if (!$leave) {
            return $this->sendError('Something went wrong', 400);
        }
        return $this->sendResponse($leave, 'Leave created successfully!');
    }

    // Update country
    public function update(LeaveRequest $request, $leave)
    {
        $data = $this->leaveRepository->find($leave);
        if (!$data) {
            return $this->sendError('Leave not found');
        }
        $this->leaveRepository->update($data, $request->all());
        return $this->sendResponse($leave, 'Leave updated successfully!');
    }

    // Delete country
    public function destroy($leave)
    {
        $data = $this->leaveRepository->find($leave);
        if (!$data) {
            return $this->sendError('Leave not found');
        }
        $this->leaveRepository->delete($data);
        return $this->sendSuccess('Leave deleted successfully!');
    }
}
