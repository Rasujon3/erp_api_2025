<?php

namespace App\Modules\Branches\Repositories;


use App\Helpers\ActivityLogger;
use App\Modules\Branches\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BranchRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $branches = Branch::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $branches->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $branches->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $branches->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $branches->whereNotNull('deleted_at')->count();
        $totalUpdated = $branches->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total Count is without soft-deleted
        $totalBranches = $branches->count();

        return [
            'totalBranches' => $totalBranches,
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
        $query = Branch::withTrashed(); // Load all records without soft-deleted

        if ($request->has('draft')) {
            $query->where('draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('deleted_at');
            } else {
                $query->whereNull('deleted_at');
            }
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('updated_at');
            } else {
                $query->whereNull('updated_at');
            }
        }

        $list = $query->get();
        return $list;
    }

    public function store(array $data): ?Branch
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft']) && $data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Create the country record in the database
            $branch = Branch::create($data);

            // Log activity
            ActivityLogger::log('Branch Add', 'Branch', 'Branch', $branch->id, [
                'code' => $branch->code ?? '',
                'name' => $branch->name ?? ''
            ]);

            DB::commit();

            return $branch;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Branch: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Branch $branch, array $data): ?Branch
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft']) && $data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Perform the update
            $branch->update($data);

            // Soft delete the record if 'is_delete' is 1
            if (isset($data['is_delete'])) {
                if ($data['is_delete'] == 1) {
                    $this->delete($branch);
                } else {
                    $branch->update([ 'is_deleted' => 0, 'deleted_at' => null, 'is_active' => 1 ]);
                    // Log activity for update
                    ActivityLogger::log('Branch Updated', 'Branch', 'Branch', $branch->id, [
                        'code' => $branch->code ?? '',
                        'name' => $branch->name ?? ''
                    ]);
                }

            } else {
                // Log activity for update
                ActivityLogger::log('Branch Updated', 'Branch', 'Branch', $branch->id, [
                    'code' => $branch->code ?? '',
                    'name' => $branch->name ?? ''
                ]);
            }

            DB::commit();
            return $branch;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Branch: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Branch $branch): bool
    {
        DB::beginTransaction();
        try {
            $branch->update([ 'is_deleted' => 1, 'is_active' => 0 ]);
            // Perform soft delete
            $deleted = $branch->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Branch Deleted', 'Branch', 'Branch', $branch->id, [
                'code' => $branch->code ?? '',
                'name' => $branch->name ?? ''
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Branch: ' , [
                'id' => $branch->id,
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
        return Branch::withTrashed()->find($id);
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->branches as $data) {
                $branch = Branch::find($data['id']);

                if (!$branch) {
                    continue; // Skip if not found
                }

                // Update details
                $branch->update([
                    'code' => $data['code'] ?? $branch->code,
                    'name' => $data['name'] ?? $branch->name,
                    'company_name' => $data['company_name'] ?? $branch->company_name,
                    'website' => $data['website'] ?? $branch->website,
                    'city' => $data['city'] ?? $branch->city,
                    'state' => $data['state'] ?? $branch->state,
                    'bank_id' => $data['bank_id'] ?? $branch->bank_id,
                    'country_id' => $data['country_id'] ?? $branch->country_id,
                    'currency_id' => $data['currency_id'] ?? $branch->currency_id,
                    'zip_code' => $data['zip_code'] ?? $branch->zip_code,
                    'phone' => $data['phone'] ?? $branch->phone,
                    'address' => $data['address'] ?? $branch->address,
                    'is_default' => $data['is_default'] ?? $branch->is_default,
                    'status' => $data['status'] ?? $branch->status,
                    'draft' => $data['draft'] ?? $branch->draft,
                    'drafted_at' => (isset($data['draft']) && $data['draft'] == 1) ? now() : $branch->drafted_at,
                    'is_active' => $data['is_active'] ?? $branch->is_active,
                ]);

                // Handle flag image upload if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $branch->update(['flag' => $flagPath]);
                }
                */
                // Log activity for update
                ActivityLogger::log('Branch Updated', 'Branch', 'Branch', $branch->id, [
                    'name' => $branch->name ?? '',
                    'account_number' => $branch->account_number ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk updating Branch: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/images/customer';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('customer_logo_', true) . '.' . $file->getClientOriginalExtension();

        // Move the file to the destination directory
        $file->move($directory, $fileName);

        // path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function updateFile($file, $data)
    {
        // Define the directory path
        $filePath = 'files/images/customer';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('customer_logo_', true) . '.' . $file->getClientOriginalExtension();

        // Delete the old file if it exists
        $this->deleteOldFile($data);

        // Move the new file to the destination directory
        $file->move($directory, $fileName);

        // Store path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function deleteOldFile($data)
    {
        if (!empty($data->customer_logo)) {
            $oldFilePath = public_path($data->customer_logo); // Use without prepending $filePath
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath); // Delete the old file
                return true;
            } else {
                Log::warning('Old file not found for deletion', ['path' => $oldFilePath]);
                return false;
            }
        }
    }
}
