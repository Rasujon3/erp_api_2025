<?php

namespace App\Modules\AdminGroups\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\AdminGroups\Models\AdminGroup;
use App\Modules\AdminGroups\Models\AdminGroupTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminGroupRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $cities = AdminGroup::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $cities->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $cities->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $cities->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $cities->whereNotNull('deleted_at')->count();
        $totalUpdated = $cities->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalCities = $cities->whereNull('deleted_at')->count();

        return [
            'totalCities' => $totalCities,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }

    public function list($request)
    {
        $query = AdminGroup::withTrashed()
            ->leftJoin('countries', 'admin_groups.country_id', '=', 'countries.id')
            ->select('admin_groups.*', 'countries.name as country_name');

        if ($request->has('is_draft')) {
            $query->where('admin_groups.is_draft', $request->input('is_draft'));
        }
        if ($request->has('is_active')) {
            $query->where('admin_groups.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('admin_groups.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('admin_groups.deleted_at');
            } else {
                $query->whereNull('admin_groups.deleted_at');
            }
        } else {
            $query->whereNull('admin_groups.deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('admin_groups.updated_at');
            } else {
                $query->whereNull('admin_groups.updated_at');
            }
        }
        if ($request->has('country_id')) {
            $query->where('admin_groups.country_id', $request->input('country_id'));
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?AdminGroup
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['is_draft'] == 1) {
                $data['drafted_at'] = now();
            }
            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                $data['flag'] = $this->storeFile($data['flag']);
            }

            // Create the AdminGroup record in the database
            $adminGroup = AdminGroup::create($data);

            // Log activity
            ActivityLogger::log('AdminGroup Add', 'AdminGroup', 'AdminGroup', $adminGroup->id, [
                'code' => $adminGroup->code ?? '',
                'name' => $adminGroup->name ?? '',
                'country_id' => $adminGroup->country_id ?? '',
            ]);

            DB::commit();

            return $adminGroup;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing AdminGroup: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(AdminGroup $adminGroup, array $data): ?AdminGroup
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['is_draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                $data['flag'] = $this->updateFile($data['flag'], $adminGroup);
            }

            // Perform the update
            $adminGroup->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $deleted = $this->delete($adminGroup);
                if (!$deleted) {
                    DB::rollBack();
                    return null;
                }
                $adminGroup->update(['is_deleted' => 1]);
            } else {
                // Log activity for update
                ActivityLogger::log('AdminGroup Updated', 'AdminGroup', 'AdminGroup', $adminGroup->id, [
                    'code' => $adminGroup->code ?? '',
                    'name' => $adminGroup->name ?? '',
                    'country_id' => $adminGroup->country_id ?? '',
                ]);
            }

            DB::commit();
            return $adminGroup;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating AdminGroup: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(AdminGroup $adminGroup): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $adminGroup->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('AdminGroup Deleted', 'AdminGroup', 'AdminGroup', $adminGroup->id, [
                'code' => $adminGroup->code ?? '',
                'name' => $adminGroup->name ?? '',
                'country_id' => $adminGroup->country_id ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting AdminGroup: ' , [
                'state_id' => $adminGroup->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return AdminGroup::find($id);
    }
    public function getData($id)
    {
        $adminGroup = AdminGroup::leftJoin('countries', 'admin_groups.country_id', '=', 'countries.id')
            ->where('admin_groups.id', $id)
            ->select('admin_groups.*', 'countries.name as country_name')
            ->first();
        return $adminGroup;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->adminGroups as $data) {
                $adminGroup = AdminGroup::find($data['id']);

                if (!$adminGroup) {
                    continue; // Skip if city not found
                }

                // Update state details
                $adminGroup->update([
                    'code' => $data['code'] ?? $adminGroup->code,
                    'english' => $data['english'] ?? $adminGroup->english,
                    'arabic' => $data['arabic'] ?? $adminGroup->arabic,
                    'bengali' => $data['bengali'] ?? $adminGroup->bengali,
                    'is_default' => $data['is_default'] ?? $adminGroup->is_default,
                    'is_draft' => $data['is_draft'] ?? $adminGroup->is_draft,
                    'drafted_at' => $data['is_draft'] == 1 ? now() : $adminGroup->drafted_at,
                    'is_active' => $data['is_active'] ?? $adminGroup->is_active,
                    'country_id' => $data['country_id'] ?? $adminGroup->country_id,
                ]);
                // Log activity for update
                ActivityLogger::log('AdminGroup Updated', 'AdminGroup', 'AdminGroup', $adminGroup->id, [
                    'code' => $adminGroup->code ?? '',
                    'name' => $adminGroup->name ?? '',
                    'country_id' => $adminGroup->country_id ?? '',
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating AdminGroup: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    /*
    public function checkExist($id): bool
    {
        $existOnArea = Area::where('city_id', $id)->whereNull('deleted_at')->exists();
        if ($existOnArea) {
            return true;
        }
        return false;
    }
    */
    public function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/images/adminGroup';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('flag_', true) . '.' . $file->getClientOriginalExtension();

        // Move the file to the destination directory
        $file->move($directory, $fileName);

        // path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function updateFile($file, $data)
    {
        // Define the directory path
        $filePath = 'files/images/adminGroup';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('flag_', true) . '.' . $file->getClientOriginalExtension();

        // Delete the old file if it exists
        $this->deleteOldFile($data->flag);

        // Move the new file to the destination directory
        $file->move($directory, $fileName);

        // Store path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function deleteOldFile($filePath)
    {
        if (!empty($filePath)) {
            $oldFilePath = public_path($filePath); // Use without prepending $filePath
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath); // Delete the old file
                return true;
            } else {
                Log::warning('Old file not found for deletion', ['path' => $oldFilePath]);
                return false;
            }
        }
    }
    public function templateList()
    {
        $templateList = AdminGroupTemplate::get();
        return $templateList;
    }
}
