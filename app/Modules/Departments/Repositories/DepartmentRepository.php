<?php

namespace App\Modules\Departments\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Departments\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DepartmentRepository
{
    public function getSummaryData()
    {
        $departments = Department::withTrashed()->get(); // Load all records including soft-deleted

        $totalDepartments = $departments->count();

        return [
            'totalDepartments' => $totalDepartments,
        ];
    }
    public function all()
    {
        return Department::cursor(); // Load all records
    }

    public function store(array $data): ?Department
    {
        try {
            DB::beginTransaction();

            // Create the Department record in the database
            $department = Department::create($data);

            // Log activity
            ActivityLogger::log('Department Add', 'Departments', 'Department', $department->id, [
                'name' => $department->name ?? '',
                'description' => $department->description ?? ''
            ]);

            DB::commit();

            return $department;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Department: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Department $department, array $data): ?Department
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $department->update($data);
            // Log activity for update
            ActivityLogger::log('Department Updated', 'Departments', 'Department', $department->id, [
                'name' => $department->name
            ]);

            DB::commit();
            return $department;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Department: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Department $department): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $department->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Department Deleted', 'Departments', 'Department', $department->id, [
                'name' => $department->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Department: ' . $e->getMessage(), [
                'department_id' => $department->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Department::find($id);
    }
    public function getData($id)
    {
        $store = Department::where('id', $id)->first();
        return $store;
    }
    public function checkExist($id)
    {
        $exist = Department::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
