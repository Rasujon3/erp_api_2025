<?php

namespace App\Modules\Groups\Controllers;

use App\Modules\Groups\Queries\GroupDatatable;
use App\Modules\Groups\Repositories\GroupRepository;
use App\Modules\Groups\Requests\GroupRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class GroupController extends AppBaseController
{
    protected GroupRepository $groupRepository;
    protected GroupDatatable $groupDatatable;

    public function __construct(GroupRepository $groupRepo, GroupDatatable $groupDatatable)
    {
        $this->groupRepository = $groupRepo;
        $this->groupDatatable = $groupDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->groupRepository->all();
        return $this->sendResponse($statues, 'Groups retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->groupRepository->getSummaryData();
        return $this->sendResponse($summary, 'Group summary retrieved successfully.');
    }


    // Get DataTable records
    public function getTagsDataTable(Request $request)
    {
        $data = GroupDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Group DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Group $state)
    public function show($group)
    {
        $data = $this->groupRepository->find($group);
        // check if city exists
        if (!$data) {
            return $this->sendError('Group not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($group);
        return $this->sendResponse($data, 'Group retrieved successfully.');
    }

    public function store(GroupRequest $request)
    {
        $group = $this->groupRepository->store($request->all());
        return $this->sendResponse($group, 'Group created successfully!');
    }

    // Update country
    public function update(GroupRequest $request, $group)
//    public function update(Request $request, Country $country)
    {
        $data = $this->groupRepository->find($group);
        if (!$data) {
            return $this->sendError('Group not found');
        }
        $this->groupRepository->update($data, $request->all());
        return $this->sendResponse($group, 'Group updated successfully!');
    }

    // Delete country
//    public function destroy(Group $state)
    public function destroy($group)
    {
        $data = $this->groupRepository->find($group);
        // check if state exists
        if (!$data) {
            return $this->sendError('Group not found');
        }
        $checkExist = $this->groupRepository->checkExist($group);
        if ($checkExist) {
            return $this->sendError('Group already used, cannot be deleted', 400);
        }
        $this->groupRepository->delete($data);
        return $this->sendSuccess('Group deleted successfully!');
    }
}
