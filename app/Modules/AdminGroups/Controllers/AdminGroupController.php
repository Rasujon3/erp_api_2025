<?php

namespace App\Modules\AdminGroups\Controllers;

use App\Modules\AdminGroups\Repositories\AdminGroupRepository;
use App\Modules\AdminGroups\Requests\AdminGroupRequest;
use App\Http\Controllers\AppBaseController;

class AdminGroupController extends AppBaseController
{
    protected AdminGroupRepository $adminGroupRepository;
    public function __construct(AdminGroupRepository $adminGroupRepo)
    {
        $this->adminGroupRepository = $adminGroupRepo;
    }
    // Fetch all data
    public function index(AdminGroupRequest $request)
    {
        $statues = $this->adminGroupRepository->all($request);
        return $this->sendResponse($statues, 'Admin Groups retrieved successfully.');
    }

    // Store data
    public function store(AdminGroupRequest $request)
    {
        $adminGroup = $this->adminGroupRepository->store($request->all());
        if (!$adminGroup) {
            return $this->sendError('Something went wrong!!! [AGS-01]', 500);
        }
        return $this->sendResponse($adminGroup, 'Admin Group created successfully!');
    }

    // Get single details data
    public function show($adminGroup)
    {
        $data = $this->adminGroupRepository->find($adminGroup);
        if (!$data) {
            return $this->sendError('Admin Group not found');
        }
        $summary = $this->adminGroupRepository->getData($adminGroup);
        return $this->sendResponse($summary, 'Admin Group retrieved successfully.');
    }
    // Update data
    public function update(AdminGroupRequest $request, $adminGroup)
    {
        $data = $this->adminGroupRepository->find($adminGroup);
        if (!$data) {
            return $this->sendError('Admin Group not found');
        }
        $updated = $this->adminGroupRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [AGU-04]', 500);
        }
        return $this->sendResponse($adminGroup, 'Admin Group updated successfully!');
    }
    // bulk update
    public function bulkUpdate(AdminGroupRequest $request)
    {
        $bulkUpdate = $this->adminGroupRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [AGBU-03]', 500);
        }
        return $this->sendResponse([],'Admin Group Bulk updated successfully!');
    }
}
