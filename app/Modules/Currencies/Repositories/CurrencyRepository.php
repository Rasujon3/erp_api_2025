<?php

namespace App\Modules\Currencies\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Currencies\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrencyRepository
{
    public function all()
    {
        $list = Currency::cursor(); // Load all records without soft-deleted
        $currencies = Currency::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $currencies->where('draft', true)->count();
        $totalInactive = $currencies->where('is_active', false)->count();
        $totalActive = $currencies->where('is_active', true)->count();
        $totalDeleted = $currencies->whereNotNull('deleted_at')->count();
        $totalUpdated = $currencies->whereNotNull('updated_at')->count();

        // Ensure totalCurrencies is the sum of totalDraft + totalInactive + totalActive
        $totalCurrencies = $totalDraft + $totalInactive + $totalActive + $totalDeleted;
        return [
            'totalCurrencies' => $totalCurrencies,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }

    public function store(array $data): ?Currency
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Handle file upload for 'flag'
            if (isset($data['symbol']) && $data['symbol'] instanceof \Illuminate\Http\UploadedFile) {
                $data['symbol'] = $this->storeFile($data['symbol']);
            }

            // Create the country record in the database
            $currency = Currency::create($data);

            // Log activity
            ActivityLogger::log('Currency Add', 'Currency', 'Currency', $currency->id, [
                'name' => $currency->name ?? '',
                'code' => $currency->code ?? ''
            ]);

            DB::commit();

            return $currency;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Currency: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Currency $currency, array $data): ?Currency
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Handle file upload for 'flag'
            if (isset($data['symbol']) && $data['symbol'] instanceof \Illuminate\Http\UploadedFile) {
                $data['symbol'] = $this->updateFile($data['symbol'], $currency);
            }

            // Perform the update
            $currency->update($data);

            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($currency);
            } else {
                // Log activity for update
                ActivityLogger::log('Currency Updated', 'Currency', 'Currency', $currency->id, [
                    'name' => $currency->name
                ]);
            }

            DB::commit();
            return $currency;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Currency: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Currency $currency): bool
    {
        try {
            DB::beginTransaction();
            // Attempt to delete flag image if it exists
            $deleteOldFile = $this->deleteOldFile($currency);
            // if delete old file, then update country table on flag column is null
            if ($deleteOldFile) {
                $currency->update(['symbol' => null]);
            }
            // Perform soft delete
            $deleted = $currency->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Currency Deleted', 'Currency', 'Currency', $currency->id, [
                'name' => $currency->name ?? '',
                'code' => $currency->code ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Currency: ' , [
                'country_id' => $currency->id,
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
        return Currency::find($id);
    }
    public function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/images/currency';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('symbol_', true) . '.' . $file->getClientOriginalExtension();

        // Move the file to the destination directory
        $file->move($directory, $fileName);

        // path & file name in the database
        $path = $filePath . '/' . $fileName;
        return $path;
    }
    public function updateFile($file, $data)
    {
        // Define the directory path
        $filePath = 'files/images/currency';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('symbol_', true) . '.' . $file->getClientOriginalExtension();

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
        if (!empty($data->symbol)) {
            $oldFilePath = public_path($data->symbol); // Use without prepending $filePath
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
            foreach ($request->currencies as $data) {
                $currency = Currency::find($data['id']);

                if (!$currency) {
                    continue; // Skip if country is not found
                }

                // Update country details
                $currency->update([
                    'code' => $data['code'] ?? $currency->code,
                    'name' => $data['name'] ?? $currency->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $currency->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $currency->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $currency->is_default,
                    'draft' => $data['draft'] ?? $currency->draft,
                    'drafted_at' => $data['draft'] == 1 ? now() : $currency->drafted_at,
                    'is_active' => $data['is_active'] ?? $currency->is_active,
                    'exchange' => $data['exchange'] ?? $currency->exchange,
                ]);

                // Handle flag image upload if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $currency->update(['flag' => $flagPath]);
                }
                */
                // Log activity for update
                ActivityLogger::log('Currency Updated', 'Currency', 'Currency', $currency->id, [
                    'name' => $currency->name ?? '',
                    'code' => $currency->code ?? '',
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk updating country: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
