<?php

namespace App\Modules\LeaveApplications\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\LeaveApplications\Models\LeaveApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LeaveApplicationRepository
{
    public function getSummaryData()
    {
        $leaveApplications = LeaveApplication::withTrashed()->get(); // Load all records including soft-deleted

        $totalLeaveApplications = $leaveApplications->count();

        return [
            'totalLeaveApplications' => $totalLeaveApplications,
        ];
    }
    public function all()
    {
        return LeaveApplication::cursor(); // Load all records including soft-deleted
    }

    public function store(array $data): ?LeaveApplication
    {
        try {
            DB::beginTransaction();

            if (isset($data['hard_copy']) && $data['hard_copy'] instanceof \Illuminate\Http\UploadedFile) {
                $data['hard_copy'] = $this->storeFile($data['hard_copy']);
            }

            // Create the LeaveApplication record in the database
            $leaveApplication = LeaveApplication::create($data);

            // Log activity
            ActivityLogger::log('LeaveApplication Add', 'Leaves', 'LeaveApplication', $leaveApplication->id, [
                'name' => $leaveApplication->name ?? '',
                'description' => $leaveApplication->description ?? ''
            ]);

            DB::commit();

            return $leaveApplication;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing LeaveApplication: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(LeaveApplication $leaveApplication, array $data): ?LeaveApplication
    {
        try {
            DB::beginTransaction();

            // Handle file upload for 'image'
            if (isset($data['hard_copy']) && $data['hard_copy'] instanceof \Illuminate\Http\UploadedFile) {
                $data['hard_copy'] = $this->updateFile($data['hard_copy'], $leaveApplication);
            }

            // Perform the update
            $leaveApplication->update($data);

            // Log activity for update
            ActivityLogger::log('LeaveApplication Updated', 'Leaves', 'LeaveApplication', $leaveApplication->id, [
                'name' => $leaveApplication->name
            ]);

            DB::commit();
            return $leaveApplication;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating LeaveApplication: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    /**
     * @throws Exception
     */
    public function delete(LeaveApplication $leaveApplication): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $leaveApplication->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('LeaveApplication Deleted', 'Leaves', 'LeaveApplication', $leaveApplication->id, [
                'name' => $leaveApplication->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting LeaveApplication: ' . $e->getMessage(), [
                'department_id' => $leaveApplication->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return LeaveApplication::find($id);
    }

    public function getData($id)
    {
        $data = LeaveApplication::leftJoin('leaves', 'leaves.id', '=', 'leave_applications.leave_id')
            ->leftJoin('employees', 'employees.id', '=', 'leave_applications.employee_id')
            ->where('leave_applications.id', $id)
            ->select('leave_applications.*', 'leaves.name as leave_name', 'employees.name as employee_name')
            ->first();
        return $data;
    }
    public function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/images/leaveApplication';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('leaveApplication_', true) . '.' . $file->getClientOriginalExtension();

        // Move the file to the destination directory
        $file->move($directory, $fileName);

        // path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function updateFile($file, $data)
    {
        // Define the directory path
        $filePath = 'files/images/leaveApplication';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('leaveApplication_', true) . '.' . $file->getClientOriginalExtension();

        // Delete the old file if it exists
        if (!empty($data->hard_copy)) {
            $oldFilePath = public_path($data->hard_copy); // Use without prepending $filePath
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath); // Delete the old file
            } else {
                Log::warning('Old file not found for deletion', ['path' => $oldFilePath]);
            }
        }

        // Move the new file to the destination directory
        $file->move($directory, $fileName);

        // Store path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
}
