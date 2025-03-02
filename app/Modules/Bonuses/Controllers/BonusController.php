<?php

namespace App\Modules\Bonuses\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Bonuses\Repositories\BonusRepository;
use App\Modules\Bonuses\Requests\BonusRequest;

class BonusController extends AppBaseController
{
    protected BonusRepository $BonusRepository;

    public function __construct(BonusRepository $BonusRepo)
    {
        $this->BonusRepository = $BonusRepo;
    }
    // Fetch all data
    public function index(BonusRequest $request)
    {
        $data = $this->BonusRepository->all($request);
        return $this->sendResponse($data, 'Bonuses retrieved successfully.');
    }

    // Store data
    public function store(BonusRequest $request)
    {
        $bonus = $this->BonusRepository->store($request->all());
        if (!$bonus) {
            return $this->sendError('Something went wrong!!! [CS-01]', 500);
        }
        return $this->sendResponse($bonus, 'Bonus created successfully!');
    }

    // Get single details data
    public function show($bonus)
    {
        $data = $this->BonusRepository->find($bonus);
        if (!$data) {
            return $this->sendError('Bonus not found');
        }
        $summary = $this->BonusRepository->getData($bonus);
        return $this->sendResponse($summary, 'Bonus retrieved successfully.');
    }
    // Update data
    public function update(BonusRequest $request, $bonus)
    {
        $data = $this->BonusRepository->find($bonus);
        if (!$data) {
            return $this->sendError('Bonus not found');
        }
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->BonusRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('Bonus already used, cannot be deleted', 400);
            }
        }
        $updated = $this->BonusRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [CU-04]', 500);
        }
        return $this->sendResponse($bonus, 'Bonus updated successfully!');
    }
    // bulk update
    public function bulkUpdate(BonusRequest $request)
    {
        $bulkUpdate = $this->BonusRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [CBU-05]', 500);
        }
        return $this->sendResponse([],'Bonus Bulk updated successfully!');
    }
}
