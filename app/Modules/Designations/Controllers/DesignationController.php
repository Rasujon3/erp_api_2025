<?php

namespace App\Modules\Designations\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Designations\Repositories\DesignationRepository;
use App\Modules\Designations\Requests\DesignationRequest;

class DesignationController extends AppBaseController
{
    protected DesignationRepository $designationRepository;

    public function __construct(DesignationRepository $designationRepo)
    {
        $this->designationRepository = $designationRepo;
    }
    // Fetch all data
    public function index(DesignationRequest $request)
    {
        $data = $this->designationRepository->all($request);
        return $this->sendResponse($data, 'Designations retrieved successfully.');
    }

    // Store data
    public function store(DesignationRequest $request)
    {
        $designation = $this->designationRepository->store($request->all());
        if (!$designation) {
            return $this->sendError('Something went wrong!!! [DCS-01]', 500);
        }
        return $this->sendResponse($designation, 'Designation created successfully!');
    }

    // Get single details data
    public function show($designation)
    {
        $data = $this->designationRepository->find($designation);
        if (!$data) {
            return $this->sendError('Designation not found');
        }
        $summary = $this->designationRepository->getData($designation);
        return $this->sendResponse($summary, 'Designation retrieved successfully.');
    }
    // Update data
    public function update(DesignationRequest $request, $designation)
    {
        $data = $this->designationRepository->find($designation);
        if (!$data) {
            return $this->sendError('Designation not found');
        }
        /*
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->designationRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('Designation already used, cannot be deleted', 400);
            }
        }
        */
        $updated = $this->designationRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [DCU-02]', 500);
        }
        return $this->sendResponse($designation, 'Designation updated successfully!');
    }
    // bulk update
    public function bulkUpdate(DesignationRequest $request)
    {
        $bulkUpdate = $this->designationRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [DCBU-03]', 500);
        }
        return $this->sendResponse([],'Designation Bulk updated successfully!');
    }
}
