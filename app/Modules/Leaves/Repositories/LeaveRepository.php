<?php

namespace App\Modules\Leaves\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Category\Models\Category;
use App\Modules\Leaves\Models\Leave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LeaveRepository
{
    public function getSummaryData()
    {
        $leaves = Leave::withTrashed()->get(); // Load all records including soft-deleted

        $totalLeaves = $leaves->count();

        return [
            'totalLeaves' => $totalLeaves,
        ];
    }
    public function all()
    {
        return Leave::cursor(); // Load all records including soft-deleted
    }

    public function store(array $data): ?Leave
    {
        try {
            DB::beginTransaction();

            // Create the Leave record in the database
            $leave = Leave::create($data);

            // Log activity
            ActivityLogger::log('Leave Add', 'Leaves', 'Leave', $leave->id, [
                'name' => $leave->name ?? '',
                'description' => $leave->description ?? ''
            ]);

            DB::commit();

            return $leave;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Leave: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Leave $leave, array $data): ?Leave
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $leave->update($data);
            // Log activity for update
            ActivityLogger::log('Leave Updated', 'Leaves', 'Leave', $leave->id, [
                'name' => $leave->name
            ]);

            DB::commit();
            return $leave;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Leave: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    /**
     * @throws Exception
     */
    public function delete(Leave $leave): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $leave->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Leave Deleted', 'Leaves', 'Leave', $leave->id, [
                'name' => $leave->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Leave: ' . $e->getMessage(), [
                'department_id' => $leave->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Leave::find($id);
    }
}
