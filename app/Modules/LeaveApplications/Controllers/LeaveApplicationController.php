<?php

namespace App\Modules\LeaveApplications\Controllers;

use App\Modules\LeaveApplications\Queries\LeaveApplicationDatatable;
use App\Modules\LeaveApplications\Repositories\LeaveApplicationRepository;
use App\Modules\LeaveApplications\Requests\LeaveApplicationRequest;
use App\Modules\Leaves\Queries\LeaveDatatable;
use App\Modules\Leaves\Repositories\LeaveRepository;
use App\Modules\Leaves\Requests\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class LeaveApplicationController extends AppBaseController
{
    protected LeaveApplicationRepository $leaveApplicationRepository;
    protected LeaveApplicationDatatable $leaveApplicationDatatable;

    public function __construct(LeaveApplicationRepository $leaveApplicationRepo, LeaveApplicationDatatable $leaveApplicationDatatable)
    {
        $this->leaveApplicationRepository = $leaveApplicationRepo;
        $this->leaveApplicationDatatable = $leaveApplicationDatatable;
    }

    // Fetch all data
    public function index()
    {
        $leaveApplications = $this->leaveApplicationRepository->all();
        return $this->sendResponse($leaveApplications, 'Leave Applications retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->leaveApplicationRepository->getSummaryData();
        return $this->sendResponse($summary, 'Leave Application summary retrieved successfully.');
    }

    // Get DataTable records
    public function getLeaveApplicationsDataTable(Request $request)
    {
        $data = LeaveApplicationDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Leave Application DataTable data retrieved successfully.');
    }

    // Get single country details
    public function show($leaveApplication)
    {
        $data = $this->leaveApplicationRepository->find($leaveApplication);
        if (!$data) {
            return $this->sendError('Leave Application not found');
        }
        $summary = $this->leaveApplicationRepository->getData($leaveApplication);
        return $this->sendResponse($summary, 'Leave Application retrieved successfully.');
    }

    public function store(LeaveApplicationRequest $request)
    {
        $leaveApplication = $this->leaveApplicationRepository->store($request->all());
        if (!$leaveApplication) {
            return $this->sendError('Something went wrong', 400);
        }
        return $this->sendResponse($leaveApplication, 'Leave Application created successfully!');
    }

    // Update country
    public function update(LeaveApplicationRequest $request, $leaveApplication)
    {
        $data = $this->leaveApplicationRepository->find($leaveApplication);
        if (!$data) {
            return $this->sendError('Leave Application not found');
        }
        $this->leaveApplicationRepository->update($data, $request->all());
        return $this->sendResponse($leaveApplication, 'Leave Application updated successfully!');
    }

    // Delete country
    public function destroy($leaveApplication)
    {
        $data = $this->leaveApplicationRepository->find($leaveApplication);
        if (!$data) {
            return $this->sendError('Leave Application not found');
        }
        $this->leaveApplicationRepository->delete($data);
        return $this->sendSuccess('Leave Application deleted successfully!');
    }
}
