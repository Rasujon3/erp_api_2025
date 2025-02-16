<?php

namespace App\Modules\Projects\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Projects\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProjectRepository
{
    public function getSummaryData()
    {
        // $projects = Project::withTrashed()->get(); // Load all records including soft-deleted

        $totalProjects = Project::get()->count();

        return [
            'totalProjects' => $totalProjects,
        ];
    }
    public function all()
    {
        return Project::cursor(); // Load all records
    }

    public function store(array $data): ?Project
    {
        try {
            DB::beginTransaction();

            // Create the Project record in the database
            $store = Project::create($data);

            // Log activity
//            ActivityLogger::log('Country Add', 'Country', 'Country', $country->id, [
//                'name' => $country->name ?? '',
//                'code' => $country->code ?? ''
//            ]);

            DB::commit();

            return $store;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Project: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Project $project, array $data): ?Project
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $project->update($data);

            DB::commit();
            return $project;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Project: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Project $project): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $project->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
//            ActivityLogger::log('Country Deleted', 'Country', 'Country', $country->id, [
//                'name' => $country->name ?? '',
//                'code' => $country->code ?? '',
//            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Sample Category: ' . $e->getMessage(), [
                'state_id' => $project->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Project::find($id);
    }
    public function getData($id)
    {
        $store = Project::where('id', $id)->first();
        return $store;
    }
    public function checkExist($id)
    {
        $exist = Project::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
