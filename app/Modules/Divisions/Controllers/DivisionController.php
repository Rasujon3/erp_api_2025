<?php

namespace App\Modules\Divisions\Controllers;

use App\Modules\Divisions\Queries\DivisionDatatable;
use App\Modules\Divisions\Repositories\DivisionRepository;
use App\Modules\Divisions\Requests\DivisionRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class DivisionController extends AppBaseController
{
    protected DivisionRepository $divisionRepository;
    protected DivisionDatatable $divisionDatatable;

    public function __construct(DivisionRepository $divisionRepo, DivisionDatatable $divisionDatatable)
    {
        $this->divisionRepository = $divisionRepo;
        $this->divisionDatatable = $divisionDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->divisionRepository->all();
        return $this->sendResponse($statues, 'Divisions retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->divisionRepository->getSummaryData();
        return $this->sendResponse($summary, 'Division summary retrieved successfully.');
    }


    // Get DataTable records
    public function getTagsDataTable(Request $request)
    {
        $data = DivisionDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Division DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Division $state)
    public function show($division)
    {
        $data = $this->divisionRepository->find($division);
        // check if city exists
        if (!$data) {
            return $this->sendError('Division not found');
        }
        $summary = $this->divisionRepository->getData($division);
        return $this->sendResponse($summary, 'Division retrieved successfully.');
    }

    public function store(DivisionRequest $request)
    {
        $division = $this->divisionRepository->store($request->all());
        return $this->sendResponse($division, 'Division created successfully!');
    }

    // Update country
    public function update(DivisionRequest $request, $division)
//    public function update(Request $request, Country $country)
    {
        $data = $this->divisionRepository->find($division);
        // check if city exists
        if (!$data) {
            return $this->sendError('Division not found');
        }
        $this->divisionRepository->update($data, $request->all());
        return $this->sendResponse($division, 'Division updated successfully!');
    }

    // Delete country
//    public function destroy(Division $state)
    public function destroy($division)
    {
        $data = $this->divisionRepository->find($division);
        // check if state exists
        if (!$data) {
            return $this->sendError('Division not found');
        }
        $checkExist = $this->divisionRepository->checkExist($division);
        if ($checkExist) {
            return $this->sendError('Division already used, cannot be deleted', 400);
        }
        $this->divisionRepository->delete($data);
        return $this->sendSuccess('Division deleted successfully!');
    }
}
