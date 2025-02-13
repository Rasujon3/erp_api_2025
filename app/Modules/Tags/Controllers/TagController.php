<?php

namespace App\Modules\Tags\Controllers;

use App\Modules\Tags\Queries\TagDatatable;
use App\Modules\Tags\Repositories\TagRepository;
use App\Modules\Tags\Requests\TagRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class TagController extends AppBaseController
{
    protected TagRepository $tagRepository;
    protected TagDatatable $tagDatatable;

    public function __construct(TagRepository $tagRepo, TagDatatable $tagDatatable)
    {
        $this->tagRepository = $tagRepo;
        $this->tagDatatable = $tagDatatable;
    }

    // Fetch all states
    public function index()
    {
        $statues = $this->tagRepository->all();
        return $this->sendResponse($statues, 'Tags retrieved successfully.');
    }
    public function getSummary()
    {
        $summary = $this->tagRepository->getSummaryData();
        return $this->sendResponse($summary, 'Tag summary retrieved successfully.');
    }


    // Get DataTable records
    public function getTagsDataTable(Request $request)
    {
        $data = TagDatatable::getDataForDatatable($request);
        return $this->sendResponse($data, 'Tag DataTable data retrieved successfully.');
    }

    // Get single country details
//    public function show(Tag $state)
    public function show($tag)
    {
        $data = $this->tagRepository->find($tag);
        // check if city exists
        if (!$data) {
            return $this->sendError('Tag not found');
        }
//        $summary = $this->sampleCategoryRepository->getData($tag);
        return $this->sendResponse($data, 'Tag retrieved successfully.');
    }

    public function store(TagRequest $request)
    {
        $tag = $this->tagRepository->store($request->all());
        return $this->sendResponse($tag, 'Tag created successfully!');
    }

    // Update country
    public function update(TagRequest $request, $tag)
//    public function update(Request $request, Country $country)
    {
        $data = $this->tagRepository->find($tag);
        // check if city exists
        if (!$data) {
            return $this->sendError('Tag not found');
        }
        $this->tagRepository->update($data, $request->all());
        return $this->sendResponse($tag, 'Tag updated successfully!');
    }

    // Delete country
//    public function destroy(Tag $state)
    public function destroy($tag)
    {
        $data = $this->tagRepository->find($tag);
        // check if state exists
        if (!$data) {
            return $this->sendError('Tag not found');
        }
        $this->tagRepository->delete($data);
        return $this->sendSuccess('Tag deleted successfully!');
    }
}
