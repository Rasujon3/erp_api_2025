<?php

namespace App\Modules\Category\Controllers;

use App\Modules\Category\Queries\CategoryDatatable;
use App\Modules\Category\Repositories\CategoryRepository;
use App\Modules\Category\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class CategoryController extends AppBaseController
{
    protected CategoryRepository $categoryRepository;
    protected CategoryDatatable $categoryDatatable;

    public function __construct(CategoryRepository $categoryRepo, CategoryDatatable $categoryDatatable)
    {
        $this->categoryRepository = $categoryRepo;
        $this->categoryDatatable = $categoryDatatable;
    }

    // Fetch all data
    public function index()
    {
        $sizes = $this->categoryRepository->all();
        return $this->sendResponse($sizes, 'Categories retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->categoryRepository->getSummaryData();
        return $this->sendResponse($summary, 'Category summary retrieved successfully.');
    }

    // Get DataTable records
    public function getCategoryDataTable(Request $request)
    {
        $data = CategoryDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Category DataTable data retrieved successfully.');
    }

    // Get single details
    public function show($category)
    {
        $data = $this->categoryRepository->find($category);
        if (!$data) {
            return $this->sendError('Category not found');
        }
        $summary = $this->categoryRepository->getData($category);
        return $this->sendResponse($summary, 'Category retrieved successfully.');
    }

    public function store(CategoryRequest $request)
    {
        $category = $this->categoryRepository->store($request->all());
        if (!$category) {
            return $this->sendError('Something went wrong', 400);
        }
        return $this->sendResponse($category, 'Category created successfully!');
    }

    // Update country
    public function update(CategoryRequest $request, $category)
    {
        $data = $this->categoryRepository->find($category);
        if (!$data) {
            return $this->sendError('Category not found');
        }
        $this->categoryRepository->update($data, $request->all());
        return $this->sendResponse($category, 'Category updated successfully!');
    }

    // Delete country
    public function destroy($category)
    {
        $data = $this->categoryRepository->find($category);
        if (!$data) {
            return $this->sendError('Category not found');
        }
        $exist = $this->categoryRepository->checkExist($category);
        if ($exist) {
            return $this->sendError('Category already used, cannot delete');
        }
        $this->categoryRepository->delete($data);
        return $this->sendSuccess('Category deleted successfully!');
    }
}
