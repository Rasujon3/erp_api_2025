<?php

namespace App\Modules\Branches\Controllers;

use App\Modules\Admin\Models\Country;
use App\Modules\Areas\Queries\AreaDatatable;
use App\Modules\Areas\Repositories\AreaRepository;
use App\Modules\Areas\Requests\AreaRequest;
use App\Modules\Branches\Queries\BranchDatatable;
use App\Modules\Branches\Repositories\BranchRepository;
use App\Modules\Branches\Requests\BranchRequest;
use App\Modules\City\Queries\CityDatatable;
use App\Modules\City\Repositories\CityRepository;
use App\Modules\City\Requests\CityRequest;
use App\Modules\States\Models\State;
use App\Modules\States\Queries\StateDatatable;
use App\Modules\States\Repositories\StateRepository;
use App\Modules\States\Requests\StateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class BranchController extends AppBaseController
{
    protected BranchRepository $branchRepository;
    protected BranchDatatable $areaDatatable;

    public function __construct(BranchRepository $areaRepo, BranchDatatable $branchDatatable)
    {
        $this->branchRepository = $areaRepo;
        $this->areaDatatable = $branchDatatable;
    }

    // Fetch all states
    public function index()
    {
        $areas = $this->branchRepository->all();
        return $this->sendResponse($areas, 'Branches retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->branchRepository->getSummaryData();
        return $this->sendResponse($summary, 'Branch summary retrieved successfully.');
    }


    // Get DataTable records
    public function getAreasDataTable(Request $request)
    {
        $data = BranchDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Branch DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Branch $state)
    public function show($branch)
    {
        $data = $this->branchRepository->find($branch);
        // check if city exists
        if (!$data) {
            return $this->sendError('Branch not found');
        }
        $summary = $this->branchRepository->getData($branch);
        return $this->sendResponse($summary, 'Branch retrieved successfully.');
    }

    public function store(BranchRequest $request)
    {
        $branch = $this->branchRepository->store($request->all());
        return $this->sendResponse($branch, 'Branch created successfully!');
    }

    // Update country
    public function update(BranchRequest $request, $branch)
    {
        $data = $this->branchRepository->find($branch);
        // check if city exists
        if (!$data) {
            return $this->sendError('Branch not found');
        }
        $this->branchRepository->update($data, $request->all());
        return $this->sendResponse($branch, 'Branch updated successfully!');
    }

    // Delete country
//    public function destroy(Branch $state)
    public function destroy($branch)
    {
        $data = $this->branchRepository->find($branch);
        // check if Branch exists
        if (!$data) {
            return $this->sendError('Branch not found');
        }
        $this->branchRepository->delete($data);
        return $this->sendSuccess('Branch deleted successfully!');
    }
}
