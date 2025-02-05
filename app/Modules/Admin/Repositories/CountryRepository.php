<?php

namespace App\Modules\Admin\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
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
        return Country::cursor();
    }

    public function store(array $data): ?Country
    {
        try {
            DB::beginTransaction();

            // Convert checkbox values to boolean (1 or 0)
            $data['is_default'] = isset($data['is_default']) && $data['is_default'] === 'on' ? 1 : 0;
            $data['draft'] = isset($data['draft']) && $data['draft'] === 'on' ? 1 : 0;
            $data['is_active'] = isset($data['is_active']) && $data['is_active'] === 'on' ? 1 : 0;

            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] === 1) {
                $data['drafted_at'] = now();
            }

            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                // Define the directory path
                $directory = public_path('files/images/country');

                // Ensure the directory exists
                if (!file_exists($directory)) {
                    mkdir($directory, 0777, true);
                }

                // Generate a unique file name
                $fileName = uniqid('flag_', true) . '.' . $data['flag']->getClientOriginalExtension();

                // Move the file to the destination directory
                $data['flag']->move($directory, $fileName);

                // Save only the file name in the database
                $data['flag'] = $fileName;
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
            Log::error('Error in storing country: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Country $country, array $data): ?Country
    {
        try {
            DB::beginTransaction();

            // Convert checkbox values to boolean (1 or 0)
            $data['is_default'] = isset($data['is_default']) && $data['is_default'] === 'on' ? 1 : 0;
            $data['draft'] = isset($data['draft']) && $data['draft'] === 'on' ? 1 : 0;
            $data['is_active'] = isset($data['is_active']) && $data['is_active'] === 'on' ? 1 : 0;

            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] === 1) {
                $data['drafted_at'] = now();
            }

            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                $directory = public_path('files/images/country');

                // Ensure the directory exists
                if (!file_exists($directory)) {
                    mkdir($directory, 0777, true);
                }

                // Generate a unique file name
                $fileName = uniqid('flag_', true) . '.' . $data['flag']->getClientOriginalExtension();

                // Delete the old image if it exists
                if ($country->flag && file_exists(public_path("files/images/country/{$country->flag}"))) {
                    unlink(public_path("files/images/country/{$country->flag}"));
                }

                // Move the new file to the destination directory
                $data['flag']->move($directory, $fileName);
                $data['flag'] = $fileName;
            }

            // Perform the update
            $country->update($data);

            // Soft delete the record if 'is_delete' is set to 'on'
            if (!empty($data['is_delete']) && $data['is_delete'] === 'on') {
                $country->delete(); // Soft delete (set deleted_at timestamp)
                ActivityLogger::log('Country Deleted', 'Country', 'Country', $country->id, [
                    'name' => $country->name
                ]);
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
            Log::error('Error updating country: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function updateFromDataTable(array $data)
    {
        try {
            DB::beginTransaction();

            // Find country
            $country = Country::find($data['id'] ?? null);
            if (!$country) {
                return ['success' => false, 'message' => 'Country not found'];
            }

            // Ensure required fields exist
            $updatedData = [];
            if (!empty($data['code'])) {
                $updatedData['code'] = $data['code'];
            }
            if (!empty($data['name'])) {
                $updatedData['name'] = $data['name'];
            }

            // Prevent updating with empty values
            if (empty($updatedData)) {
                return ['success' => false, 'message' => 'No valid data to update'];
            }

            // Perform the update
            $country->update($updatedData);

            // Log activity
            ActivityLogger::log('Country Updated', 'Country', 'Country', $country->id, [
                'name' => $country->name,
                'code' => $country->code,
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'Country updated successfully', 'data' => $country];
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating country from DataTable: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'message' => 'An error occurred while updating the country'];
        }
    }


    public function delete(Country $country): bool
    {
        try {
            DB::beginTransaction();
            // Attempt to delete flag image if it exists
            if ($country->flag) {
                $flagPath = public_path('files/images/country/' . $country->flag);
                if (file_exists($flagPath)) {
                    try {
                        unlink($flagPath);
                    } catch (Exception $e) {
                        Log::warning('Failed to delete country flag: ' . $e->getMessage(), [
                            'flag' => $country->flag,
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
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
            Log::error('Error deleting country: ' . $e->getMessage(), [
                'country_id' => $country->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Country::findOrFail($id);
    }
}
