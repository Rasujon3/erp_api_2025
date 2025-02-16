<?php

namespace App\Modules\Projects\Controllers;

use App\Modules\Brands\Queries\BrandDatatable;
use App\Modules\Brands\Repositories\BrandRepository;
use App\Modules\Brands\Requests\BrandRequest;
use App\Modules\Colors\Queries\ColorDatatable;
use App\Modules\Colors\Repositories\ColorRepository;
use App\Modules\Colors\Requests\ColorRequest;
use App\Modules\Groups\Queries\GroupDatatable;
use App\Modules\Groups\Repositories\GroupRepository;
use App\Modules\Groups\Requests\GroupRequest;
use App\Modules\Projects\Queries\ProjectDatatable;
use App\Modules\Projects\Repositories\ProjectRepository;
use App\Modules\Projects\Requests\ProjectRequest;
use App\Modules\Sizes\Queries\SizeDatatable;
use App\Modules\Sizes\Repositories\SizeRepository;
use App\Modules\Sizes\Requests\SizeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class ProjectController extends AppBaseController
{
    protected ProjectRepository $projectRepository;
    protected ProjectDatatable $projectDatatable;

    public function __construct(ProjectRepository $projectRepo, ProjectDatatable $projectDatatable)
    {
        $this->projectRepository = $projectRepo;
        $this->projectDatatable = $projectDatatable;
    }

    // Fetch all states
    public function index()
    {
        $sizes = $this->projectRepository->all();
        return $this->sendResponse($sizes, 'Projects retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->projectRepository->getSummaryData();
        return $this->sendResponse($summary, 'Project summary retrieved successfully.');
    }

    // Get DataTable records
    public function getSizesDataTable(Request $request)
    {
        $data = ProjectDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Project DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Project $state)
    public function show($project)
    {
        $data = $this->projectRepository->find($project);
        // check if city exists
        if (!$data) {
            return $this->sendError('Project not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($project);
        return $this->sendResponse($data, 'Project retrieved successfully.');
    }

    public function store(ProjectRequest $request)
    {
        $project = $this->projectRepository->store($request->all());
        return $this->sendResponse($project, 'Project created successfully!');
    }

    // Update country
    public function update(ProjectRequest $request, $project)
//    public function update(Request $request, Country $country)
    {
        $data = $this->projectRepository->find($project);
        if (!$data) {
            return $this->sendError('Project not found');
        }
        $this->projectRepository->update($data, $request->all());
        return $this->sendResponse($project, 'Project updated successfully!');
    }

    // Delete country
//    public function destroy(Project $state)
    public function destroy($project)
    {
        $data = $this->projectRepository->find($project);
        if (!$data) {
            return $this->sendError('Project not found');
        }
        $this->projectRepository->delete($data);
        return $this->sendSuccess('Project deleted successfully!');
    }
}
