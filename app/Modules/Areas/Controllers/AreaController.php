<?php

namespace App\Modules\Areas\Controllers;

use App\Modules\Areas\Queries\AreaDatatable;
use App\Modules\Areas\Repositories\AreaRepository;
use App\Modules\Areas\Requests\AreaRequest;
use App\Http\Controllers\AppBaseController;

class AreaController extends AppBaseController
{
    protected AreaRepository $areaRepository;
    protected AreaDatatable $areaDatatable;

    public function __construct(AreaRepository $areaRepo, AreaDatatable $areaDatatable)
    {
        $this->areaRepository = $areaRepo;
        $this->areaDatatable = $areaDatatable;
    }
    // Fetch all data
    public function index(AreaRequest $request)
    {
        $data = $this->areaRepository->all($request);
        return $this->sendResponse($data, 'Areas retrieved successfully.');
    }

    // Store data
    public function store(AreaRequest $request)
    {
        $area = $this->areaRepository->store($request->all());
        if (!$area) {
            return $this->sendError('Something went wrong!!! [AS-01]', 500);
        }
        return $this->sendResponse($area, 'Area created successfully!');
    }

    // Get single details data
    public function show($area)
    {
        $data = $this->areaRepository->find($area);
        if (!$data) {
            return $this->sendError('Area not found');
        }
        $summary = $this->areaRepository->getData($area);
        return $this->sendResponse($summary, 'Area retrieved successfully.');
    }
    // Update data
    public function update(AreaRequest $request, $area)
    {
        $data = $this->areaRepository->find($area);
        if (!$data) {
            return $this->sendError('Area not found');
        }
        $updated = $this->areaRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [AU-04]', 500);
        }
        return $this->sendResponse($area, 'Area updated successfully!');
    }
    // bulk update
    public function bulkUpdate(AreaRequest $request)
    {
        $bulkUpdate = $this->areaRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [ABU-05]', 500);
        }
        return $this->sendResponse([],'Area Bulk updated successfully!');
    }
}
