<?php

namespace App\Modules\Sample\Controllers;

use App\Modules\Sample\Queries\SampleCategoryDatatable;
use App\Modules\Sample\Repositories\SampleCategoryRepository;
use App\Modules\Sample\Requests\SampleCategoryRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class SampleCategoryController extends AppBaseController
{
    protected SampleCategoryRepository $sampleCategoryRepository;
    protected SampleCategoryDatatable $sampleCategoryDatatable;

    public function __construct(SampleCategoryRepository $sampleCategoryRepo, SampleCategoryDatatable $sampleCategoryDatatable)
    {
        $this->sampleCategoryRepository = $sampleCategoryRepo;
        $this->sampleCategoryDatatable = $sampleCategoryDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->sampleCategoryRepository->all();
        return $this->sendResponse($statues, 'Sample Categories retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->sampleCategoryRepository->getSummaryData();
        return $this->sendResponse($summary, 'Sample Category summary retrieved successfully.');
    }


    // Get DataTable records
    public function getSampleCategoriesDataTable(Request $request)
    {
        $data = SampleCategoryDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Sample Category DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Sample Category $state)
    public function show($category)
    {
        $data = $this->sampleCategoryRepository->find($category);
        // check if city exists
        if (!$data) {
            return $this->sendError('Sample Category not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($category);
        return $this->sendResponse($data, 'Sample Category retrieved successfully.');
    }

    public function store(SampleCategoryRequest $request)
    {
        $category = $this->sampleCategoryRepository->store($request->all());
        return $this->sendResponse($category, 'Sample Category created successfully!');
    }

    // Update country
    public function update(SampleCategoryRequest $request, $category)
//    public function update(Request $request, Country $country)
    {
        $data = $this->sampleCategoryRepository->find($category);
        // check if city exists
        if (!$data) {
            return $this->sendError('Sample Category not found');
        }
        $this->sampleCategoryRepository->update($data, $request->all());
        return $this->sendResponse($category, 'Sample Category updated successfully!');
    }

    // Delete country
//    public function destroy(Sample Category $state)
    public function destroy($category)
    {
        $data = $this->sampleCategoryRepository->find($category);
        // check if state exists
        if (!$data) {
            return $this->sendError('Sample Category not found');
        }
        $exist = $this->sampleCategoryRepository->checkExist($category);
        if ($exist) {
            return $this->sendError('Sample Category is in use', 400);
        }
        $this->sampleCategoryRepository->delete($data);
        return $this->sendSuccess('Sample Category deleted successfully!');
    }
}
