<?php

namespace App\Modules\Departments\Controllers;

use App\Modules\Brands\Queries\BrandDatatable;
use App\Modules\Brands\Repositories\BrandRepository;
use App\Modules\Brands\Requests\BrandRequest;
use App\Modules\Colors\Queries\ColorDatatable;
use App\Modules\Colors\Repositories\ColorRepository;
use App\Modules\Colors\Requests\ColorRequest;
use App\Modules\Departments\Queries\DepartmentDatatable;
use App\Modules\Departments\Repositories\DepartmentRepository;
use App\Modules\Departments\Requests\DepartmentRequest;
use App\Modules\Groups\Queries\GroupDatatable;
use App\Modules\Groups\Repositories\GroupRepository;
use App\Modules\Groups\Requests\GroupRequest;
use App\Modules\Sizes\Queries\SizeDatatable;
use App\Modules\Sizes\Repositories\SizeRepository;
use App\Modules\Sizes\Requests\SizeRequest;
use App\Modules\Units\Queries\UnitDatatable;
use App\Modules\Units\Repositories\UnitRepository;
use App\Modules\Units\Requests\UnitRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class DepartmentController extends AppBaseController
{
    protected DepartmentRepository $departmentRepository;
    protected DepartmentDatatable $departmentDatatable;

    public function __construct(DepartmentRepository $departmentRepo, DepartmentDatatable $departmentDatatable)
    {
        $this->departmentRepository = $departmentRepo;
        $this->departmentDatatable = $departmentDatatable;
    }

    // Fetch all states
    public function index()
    {
        $sizes = $this->departmentRepository->all();
        return $this->sendResponse($sizes, 'Departments retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->departmentRepository->getSummaryData();
        return $this->sendResponse($summary, 'Department summary retrieved successfully.');
    }

    // Get DataTable records
    public function getDepartmentsDataTable(Request $request)
    {
        $data = DepartmentDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Department DataTable data retrieved successfully.');
    }

    // Get single country details
    public function show($department)
    {
        $data = $this->departmentRepository->find($department);
        if (!$data) {
            return $this->sendError('Department not found');
        }
        return $this->sendResponse($data, 'Department retrieved successfully.');
    }

    public function store(DepartmentRequest $request)
    {
        $department = $this->departmentRepository->store($request->all());
        if (!$department) {
            return $this->sendError('Something went wrong', 400);
        }
        return $this->sendResponse($department, 'Department created successfully!');
    }

    // Update country
    public function update(DepartmentRequest $request, $department)
    {
        $data = $this->departmentRepository->find($department);
        if (!$data) {
            return $this->sendError('Department not found');
        }
        $this->departmentRepository->update($data, $request->all());
        return $this->sendResponse($department, 'Department updated successfully!');
    }

    // Delete country
    public function destroy($department)
    {
        $data = $this->departmentRepository->find($department);
        if (!$data) {
            return $this->sendError('Department not found');
        }
        $this->departmentRepository->delete($data);
        return $this->sendSuccess('Department deleted successfully!');
    }
}
