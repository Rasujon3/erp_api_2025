<?php

namespace App\Modules\Designations\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Designations\Models\Designation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DesignationRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $designations = Designation::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $designations->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $designations->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $designations->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $designations->whereNotNull('deleted_at')->count();
        $totalUpdated = $designations->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalLoans = $designations->whereNull('deleted_at')->count();

        return [
            'totalLoans' => $totalLoans,
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
        $query = Designation::withTrashed()
            ->leftJoin('departments', 'designations.department_id', '=', 'departments.id')
            ->leftJoin('sub_departments', 'designations.sub_department_id', '=', 'sub_departments.id')
            ->select(
                'designations.*',
                'departments.name as department_name',
                'sub_departments.name as sub_department_name'
            );

        if ($request->has('draft')) {
            $query->where('designations.draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('designations.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('designations.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('designations.deleted_at');
            } else {
                $query->whereNull('designations.deleted_at');
            }
        } else {
            $query->whereNull('designations.deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('designations.updated_at');
            } else {
                $query->whereNull('designations.updated_at');
            }
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?Designation
    {
        DB::beginTransaction();
        try {
            /*
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Create the Designation record in the database
            $designation = Designation::create($data);

            // Log activity
            ActivityLogger::log('Designation Add', 'Designation', 'Designation', $designation->id, [
                'name' => $designation->name ?? '',
                'department_id' => $designation->department_id ?? ''
            ]);

            DB::commit();

            return $designation;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Designation: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Designation $designation, array $data): ?Designation
    {
        DB::beginTransaction();
        try {
            /*
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Perform the update
            $designation->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($designation);
            } else {
                // Log activity for update
                ActivityLogger::log('Designation Updated', 'Designation', 'Designation', $designation->id, [
                    'name' => $designation->name ?? '',
                    'department_id' => $designation->department_id ?? ''
                ]);
            }

            DB::commit();
            return $designation;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Designation: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Designation $designation): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $designation->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Designation Deleted', 'Designation', 'Designation', $designation->id, [
                'name' => $designation->name ?? '',
                'department_id' => $designation->department_id ?? ''
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Designation: ' , [
                'state_id' => $designation->id,
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
        return Designation::find($id);
    }
    public function getData($id)
    {
        $designation = Designation::leftJoin('departments', 'designations.department_id', '=', 'departments.id')
            ->leftJoin('sub_departments', 'designations.sub_department_id', '=', 'sub_departments.id')
            ->where('designations.id', $id)
            ->select(
                'designations.*',
                'departments.name as department_name',
                'sub_departments.name as sub_department_name'
            )
            ->first();
        return $designation;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->designations as $data) {
                $designation = Designation::find($data['id']);

                if (!$designation) {
                    continue; // Skip if not found
                }

                // Update state details
                $designation->update([
                    'name' => $data['name'] ?? $designation->name,
                    'description' => $data['description'] ?? $designation->description,
                    'department_id' => $data['department_id'] ?? $designation->department_id,
                    'sub_department_id' => $data['sub_department_id'] ?? $designation->sub_department_id,
                ]);
                // Log activity for update
                ActivityLogger::log('Designation Updated', 'Designation', 'Designation', $designation->id, [
                    'name' => $designation->name ?? '',
                    'department_id' => $designation->department_id ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Designation: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function checkExist($id): bool
    {
        $existOnArea = Area::where('city_id', $id)->whereNull('deleted_at')->exists();
        if ($existOnArea) {
            return true;
        }
        return false;
    }
}
