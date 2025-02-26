<?php

namespace App\Modules\Countries\Repositories;

use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use App\Modules\Countries\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CountryRepository
{
    public function getSummaryData()
    {
        $countries = Country::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $countries->where('draft', true)->count();
        $totalInactive = $countries->where('is_active', false)->count();
        $totalActive = $countries->where('is_active', true)->count();
        $totalDeleted = $countries->whereNotNull('deleted_at')->count();
        $totalUpdated = $countries->whereNotNull('updated_at')->count();

        // Ensure totalCountries is the sum of totalDraft + totalInactive + totalActive
        $totalCountries = $totalDraft + $totalInactive + $totalActive + $totalDeleted;

        return [
            'totalCountries' => $totalCountries,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
        ];
    }
    public function all()
    {
        $list = Country::cursor(); // Load all records without soft-deleted
        $countries = Country::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $countries->where('draft', true)->count();
        $totalInactive = $countries->where('is_active', false)->count();
        $totalActive = $countries->where('is_active', true)->count();
        $totalDeleted = $countries->whereNotNull('deleted_at')->count();
        $totalUpdated = $countries->whereNotNull('updated_at')->count();

        // Ensure totalCountries is the sum of totalDraft + totalInactive + totalActive
        $totalCountries = $totalDraft + $totalInactive + $totalActive + $totalDeleted;
        return [
            'list' => $list,
            'totalCountries' => $totalCountries,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
        ];
    }

    public function store(array $data): ?Country
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                $data['flag'] = $this->storeFile($data['flag']);
            }

            // Create the country record in the database
            $country = Country::create($data);

            // Log activity
            ActivityLogger::log('Country Add', 'Country', 'Country', $country->id, [
                'name' => $country->name ?? '',
                'code' => $country->code ?? ''
            ]);

            DB::commit();

            return $country;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing country: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Country $country, array $data): ?Country
    {
        try {
            DB::beginTransaction();

            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                $data['flag'] = $this->updateFile($data['flag'], $country);
            }

            // Perform the update
            $country->update($data);

            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($country);
            } else {
                // Log activity for update
                ActivityLogger::log('Country Updated', 'Country', 'Country', $country->id, [
                    'name' => $country->name
                ]);
            }

            DB::commit();
            return $country;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating country: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Country $country): bool
    {
        try {
            DB::beginTransaction();
            // Attempt to delete flag image if it exists
            $deleteOldFile = $this->deleteOldFile($country);
            // if delete old file, then update country table on flag column is null
            if ($deleteOldFile) {
                $country->update(['flag' => null]);
            }
            // Perform soft delete
            $deleted = $country->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Country Deleted', 'Country', 'Country', $country->id, [
                'name' => $country->name ?? '',
                'code' => $country->code ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting country: ' , [
                'country_id' => $country->id,
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
        return Country::find($id);
    }
    public function storeFile($file)
    {
        // Define the directory path
        $filePath = 'files/images/country';
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
        $filePath = 'files/images/country';
        $directory = public_path($filePath);

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate a unique file name
        $fileName = uniqid('flag_', true) . '.' . $file->getClientOriginalExtension();

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
        if (!empty($data->flag)) {
            $oldFilePath = public_path($data->flag); // Use without prepending $filePath
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath); // Delete the old file
                return true;
            } else {
                Log::warning('Old file not found for deletion', ['path' => $oldFilePath]);
                return false;
            }
        }
    }
    public function getMapData()
    {
        $getMapData = Country::where('deleted_at', null)->select('id', 'name', 'code', 'flag')->get();
        return $getMapData;
    }
    public function getDataForExcel()
    {
        $getDataForExcel = Country::where('deleted_at', null)->select('id', 'name', 'code', 'created_at')->get();
        return $getDataForExcel;
    }
    public function getDataForSingleExcel($id)
    {
        $getDataForSingleExcel = Country::where('deleted_at', null)
            ->select('id', 'name', 'code', 'created_at')
            ->find($id);
        return $getDataForSingleExcel;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->countries as $data) {
                $country = Country::find($data['id']);

                if (!$country) {
                    continue; // Skip if country is not found
                }

                // Update country details
                $country->update([
                    'code' => $data['code'] ?? $country->code,
                    'name' => $data['name'] ?? $country->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $country->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $country->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $country->is_default,
                    'draft' => $data['draft'] ?? $country->draft,
                    'drafted_at' => $data['draft'] == 1 ? now() : $country->drafted_at,
                    'is_active' => $data['is_active'] ?? $country->is_active,
                ]);

                // Handle flag image upload if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $country->update(['flag' => $flagPath]);
                }
                */
                // Log activity for update
                ActivityLogger::log('Country Updated', 'Country', 'Country', $country->id, [
                    'name' => $country->name
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
    public function checkExist($id)
    {
        $existOnState = State::where('country_id', $id)->whereNull('deleted_at')->exists();
        $existOnCity = City::where('country_id', $id)->whereNull('deleted_at')->exists();
        $existOnArea = Area::where('country_id', $id)->whereNull('deleted_at')->exists();

        if ($existOnState || $existOnCity || $existOnArea) {
            return true;
        }
        return false;
    }
    public function import($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->countries as $data) {
                // create data
                $country = Country::create([
                    'code' => $data['code'] ?? '',
                    'name' => $data['name'] ?? '',
                    'name_in_bangla' => $data['name_in_bangla'] ?? null,
                    'name_in_arabic' => $data['name_in_arabic'] ?? null,
                    'is_default' => $data['is_default'] ?? 0,
                    'draft' => $data['draft'] ?? 0,
                    'drafted_at' => $data['draft'] == 1 ? now() : null,
                    'is_active' => $data['is_active'] ?? 1,
                ]);

                // Handle flag image create if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $country->update(['flag' => $flagPath]);
                }
                */
                // Log activity for update
                ActivityLogger::log('Country Created', 'Country', 'Country', $country->id, [
                    'name' => $country->name
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk import country: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
