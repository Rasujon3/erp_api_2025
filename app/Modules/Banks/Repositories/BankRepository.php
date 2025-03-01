<?php

namespace App\Modules\Banks\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Banks\Models\Bank;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BankRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $suppliers = Bank::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $suppliers->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $suppliers->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $suppliers->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $suppliers->whereNotNull('deleted_at')->count();
        $totalUpdated = $suppliers->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total Count is without soft-deleted
        $totalBanks = $suppliers->whereNull('deleted_at')->count();

        return [
            'totalBanks' => $totalBanks,
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
        $query = Bank::withTrashed(); // Load all records without soft-deleted

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
        } else {
            $query->whereNull('deleted_at');
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

    public function store(array $data): ?Bank
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            /*
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            /*
            // Handle file upload for 'flag'
            if (isset($data['customer_logo']) && $data['customer_logo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['customer_logo'] = $this->storeFile($data['customer_logo']);
            }
            */

            // Create the country record in the database
            $bank = Bank::create($data);

            // Log activity
            ActivityLogger::log('Bank Add', 'Bank', 'Bank', $bank->id, [
                'name' => $bank->name ?? '',
                'account_number' => $bank->account_number ?? ''
            ]);

            DB::commit();

            return $bank;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Bank: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Bank $bank, array $data): ?Bank
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            /*
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            /*
            // Handle file upload for 'flag'
            if (isset($data['customer_logo']) && $data['customer_logo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['customer_logo'] = $this->updateFile($data['customer_logo'], $bank);
            }
            */

            // Perform the update
            $bank->update($data);

            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($bank);
            } else {
                // Log activity for update
                ActivityLogger::log('Bank Updated', 'Bank', 'Bank', $bank->id, [
                    'name' => $bank->name ?? '',
                    'account_number' => $bank->account_number ?? ''
                ]);
            }

            DB::commit();
            return $bank;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Bank: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Bank $bank): bool
    {
        try {
            DB::beginTransaction();
            // Attempt to delete flag image if it exists
            $deleteOldFile = $this->deleteOldFile($bank);
            // if delete old file, then update country table on flag column is null
            if ($deleteOldFile) {
                $bank->update(['customer_logo' => null]);
            }
            // Perform soft delete
            $deleted = $bank->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Bank Deleted', 'Bank', 'Bank', $bank->id, [
                'company_name' => $bank->company_name ?? '',
                'phone' => $bank->phone ?? ''
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Bank: ' , [
                'country_id' => $bank->id,
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
        return Bank::find($id);
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
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->banks as $data) {
                $bank = Bank::find($data['id']);

                if (!$bank) {
                    continue; // Skip if not found
                }

                // Update details
                $bank->update([
                    'name' => $data['name'] ?? $bank->name,
                    'account_number' => $data['account_number'] ?? $bank->account_number,
                    'branch_name' => $data['branch_name'] ?? $bank->branch_name,
                    'swift_code' => $data['swift_code'] ?? $bank->swift_code,
                    'description' => $data['description'] ?? $bank->description,
                ]);

                // Handle flag image upload if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $bank->update(['flag' => $flagPath]);
                }
                */
                // Log activity for update
                ActivityLogger::log('Bank Updated', 'Bank', 'Bank', $bank->id, [
                    'name' => $bank->name ?? '',
                    'account_number' => $bank->account_number ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk updating Bank: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
