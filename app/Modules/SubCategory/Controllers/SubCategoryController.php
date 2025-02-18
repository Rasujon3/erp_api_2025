<?php

namespace App\Modules\SubCategory\Controllers;

use App\Modules\SubCategory\Queries\SubCategoryDatatable;
use App\Modules\SubCategory\Repositories\SubCategoryRepository;
use App\Modules\SubCategory\Requests\SubCategoryRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class SubCategoryController extends AppBaseController
{
    protected SubCategoryRepository $subCategoryRepository;
    protected SubCategoryDatatable $subCategoryDatatable;

    public function __construct(SubCategoryRepository $subCategoryRepo, SubCategoryDatatable $subCategoryDatatable)
    {
        $this->subCategoryRepository = $subCategoryRepo;
        $this->subCategoryDatatable = $subCategoryDatatable;
    }

    // Fetch all data
    public function index()
    {
        $sizes = $this->subCategoryRepository->all();
        return $this->sendResponse($sizes, 'Sub Categories retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->subCategoryRepository->getSummaryData();
        return $this->sendResponse($summary, 'Sub Category summary retrieved successfully.');
    }

    // Get DataTable records
    public function getSubCategoryDataTable(Request $request)
    {
        $data = SubCategoryDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Sub Category DataTable data retrieved successfully.');
    }

    // Get single details
    public function show($subCategory)
    {
        $data = $this->subCategoryRepository->find($subCategory);
        if (!$data) {
            return $this->sendError('Sub Category not found');
        }
        $summary = $this->subCategoryRepository->getData($subCategory);
        return $this->sendResponse($summary, 'Sub Category retrieved successfully.');
    }

    public function store(SubCategoryRequest $request)
    {
        $subCategory = $this->subCategoryRepository->store($request->all());
        if (!$subCategory) {
            return $this->sendError('Something went wrong', 400);
        }
        return $this->sendResponse($subCategory, 'Sub Category created successfully!');
    }

    // Update country
    public function update(SubCategoryRequest $request, $subCategory)
    {
        $data = $this->subCategoryRepository->find($subCategory);
        if (!$data) {
            return $this->sendError('Sub Category not found');
        }
        $this->subCategoryRepository->update($data, $request->all());
        return $this->sendResponse($subCategory, 'Sub Category updated successfully!');
    }

    // Delete country
    public function destroy($subCategory)
    {
        $data = $this->subCategoryRepository->find($subCategory);
        if (!$data) {
            return $this->sendError('Sub Category not found');
        }
        $this->subCategoryRepository->delete($data);
        return $this->sendSuccess('Sub Category deleted successfully!');
    }
}
