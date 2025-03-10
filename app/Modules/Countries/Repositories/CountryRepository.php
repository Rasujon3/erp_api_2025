<?php

namespace App\Modules\Countries\Repositories;

use App\Modules\AdminGroups\Models\GroupCountry;
use App\Modules\Areas\Models\Area;
use App\Modules\Branches\Models\Branch;
use App\Modules\City\Models\City;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Models\CountryHistory;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\Auth;
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
    public function all($request)
    {
        $list = $this->list($request);

        $countries = Country::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $countries->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $countries->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $countries->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $countries->whereNotNull('deleted_at')->count();
        $totalUpdated = $countries->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure totalCountries is with soft-deleted
        $totalCountries = $countries->count();

        return [
            'totalCountries' => $totalCountries,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }
    private function list($request)
    {
        $query = Country::withTrashed(); // Load all records including soft-deleted

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

    public function store(array $data): ?Country
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft'])) {
                if($data['draft'] == 1) {
                    $data['drafted_at'] = now();
                    $data['is_active'] = 0;
                } else {
                    $data['is_active'] = 1;
                }
            } else {
                $data['is_active'] = 1;
            }

            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                $data['flag'] = $this->storeFile($data['flag']);
            }

            // Create the country record in the database
            $country = Country::create($data);

            $this->countryHistoryCreate('Country Add');

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
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft'])) {
                if($data['draft'] == 1) {
                    $data['drafted_at'] = now();
                    $data['is_active'] = 0;
                } else {
                    $data['is_active'] = 1;
                }
            } else {
                $data['is_active'] = 1;
            }

            // Handle file upload for 'flag'
            if (isset($data['flag']) && $data['flag'] instanceof \Illuminate\Http\UploadedFile) {
                $data['flag'] = $this->updateFile($data['flag'], $country);
            }

            // Perform the update
            $country->update($data);

            // Soft delete the record if 'is_delete' is 1
            if (isset($data['is_delete'])) {
                if ($data['is_delete'] == 1) {
                    $this->delete($country);
                } else {
                    // restore the data from soft-deleted
                    $country->update([
                        'is_deleted' => 0,
                        'deleted_at' => null,
                        'is_active' => 1,
                        'draft' => 0
                    ]);

                    $this->countryHistoryCreate('Country Updated');
                }
            } else {
                $this->countryHistoryCreate('Country Updated');
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
        DB::beginTransaction();
        try {
            /*
            // Attempt to delete flag image if it exists
            $deleteOldFile = $this->deleteOldFile($country);
            // if delete old file, then update country table on flag column is null
            if ($deleteOldFile) {
                $country->update(['flag' => null]);
            }
            */

            $country->update([
                'is_deleted' => 1,
                'is_active' => 0,
                'draft' => 1,
                'deleted_at' => now()
            ]);
            // Perform soft delete
            $deleted = $country->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }

            $this->countryHistoryCreate('Country Deleted');
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
        return Country::withTrashed()->find($id);
    }
    private function storeFile($file)
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
    private function updateFile($file, $data)
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
    private function deleteOldFile($data)
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
                    'drafted_at' => (isset($data['draft']) && $data['draft'] == 1) ? now() : $country->drafted_at,
                    'is_active' => ((isset($data['draft']) && $data['draft'] == 1) || $country->draft == 1) ? 0 : 1,
                ]);

                // Handle flag image upload if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $country->update(['flag' => $flagPath]);
                }
                */

                $this->countryHistoryCreate('Country Updated');
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
        $existOnAdminGroup = GroupCountry::where('country_id', $id)->whereNull('deleted_at')->exists();
        $existOnBranch = Branch::where('currency_id', $id)->exists();

        if ($existOnState || $existOnCity || $existOnArea || $existOnAdminGroup || $existOnBranch) {
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

                $this->countryHistoryCreate('Country Created');
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
    private function countryHistoryCreate(string $actionType, $exportPdf = false, $exportXls = false, $exportPrint = false): bool
    {
        DB::beginTransaction();
        try {
            // Get the authenticated user
            $user = Auth::user();

            CountryHistory::create([
                'client_id' => $user->admin_client_id ?? null,
                'action_date' => now(),
                'action_by' => $user->name ?? null,
                'action_type' => $actionType,
                'export_pdf' => $exportPdf,
                'export_xls' => $exportXls,
                'export_print' => $exportPrint,
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error creating country history', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function checkAvailability(array $data): bool
    {
        try {
            $code = $data['code'] ?? null;
            $name = $data['name'] ?? null;

            // Check if either code or name already exists
            $exists = Country::where(function ($query) use ($code, $name) {
                if ($code) {
                    $query->orWhere('code', $code);
                }
                if ($name) {
                    $query->orWhere('name', $name);
                }
            })
                ->exists();

            // Return true if no match (available), false if exists (not available)
            return $exists;
        } catch (\Exception $e) {
            Log::error('Error checking country availability', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Default to false (not available) on error
            return false;
        }
    }
    public function history()
    {
        try {
            $history = CountryHistory::orderBy('id', 'desc')->get();
            return $history;
        } catch (\Exception $e) {
            Log::error('Error checking country history', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Default to false (not available) on error
            return false;
        }
    }
    public function hasData(): bool
    {
        try {
            // Check for any records, including soft-deleted ones
            return Country::withTrashed()->exists();
        } catch (\Exception $e) {
            Log::error('Error checking countries table data', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return true; // Assume data exists on error to prevent import
        }
    }
}
