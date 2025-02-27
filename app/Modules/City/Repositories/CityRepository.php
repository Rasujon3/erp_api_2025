<?php

namespace App\Modules\City\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CityRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $cities = City::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $cities->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $cities->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $cities->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $cities->whereNotNull('deleted_at')->count();
        $totalUpdated = $cities->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalCities = $cities->whereNull('deleted_at')->count();

        return [
            'totalCities' => $totalCities,
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
        $query = City::withTrashed()
            ->leftJoin('countries', 'cities.country_id', '=', 'countries.id')
            ->leftJoin('states', 'states.id', '=', 'cities.state_id')
            ->whereNull('cities.deleted_at')
            ->select('cities.*', 'countries.name as country_name', 'states.name as state_name');

        if ($request->has('draft')) {
            $query->where('cities.draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('cities.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('cities.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('cities.deleted_at');
            } else {
                $query->whereNull('cities.deleted_at');
            }
        } else {
            $query->whereNull('cities.deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('cities.updated_at');
            } else {
                $query->whereNull('cities.updated_at');
            }
        }
        if ($request->has('country_id')) {
            $query->where('cities.country_id', $request->input('country_id'));
        }
        if ($request->has('state_id')) {
            $query->where('cities.state_id', $request->input('state_id'));
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?City
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Create the City record in the database
            $city = City::create($data);

            // Log activity
            ActivityLogger::log('City Add', 'City', 'City', $city->id, [
                'name' => $city->name ?? '',
                'country_id' => $city->country_id ?? '',
                'state_id' => $city->state_id ?? ''
            ]);

            DB::commit();

            return $city;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing City: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(City $city, array $data): ?City
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Perform the update
            $city->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($city);
            } else {
                // Log activity for update
                ActivityLogger::log('City Updated', 'City', 'City', $city->id, [
                    'name' => $city->name ?? '',
                    'country_id' => $city->country_id ?? '',
                    'state_id' => $city->state_id ?? ''
                ]);
            }

            DB::commit();
            return $city;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating City: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(City $city): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $city->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('City Deleted', 'City', 'City', $city->id, [
                'name' => $city->name ?? '',
                'country_id' => $city->country_id ?? '',
                'state_id' => $city->state_id ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting City: ' , [
                'state_id' => $city->id,
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
        return City::find($id);
    }
    public function getData($id)
    {
        $city = City::leftJoin('countries', 'cities.country_id', '=', 'countries.id')
            ->leftJoin('states', 'states.id', '=', 'cities.state_id')
            ->where('cities.id', $id)
            ->select('cities.*', 'countries.name as country_name', 'states.name as state_name')
            ->first();
        return $city;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->cities as $data) {
                $city = City::find($data['id']);

                if (!$city) {
                    continue; // Skip if city not found
                }

                // Update state details
                $city->update([
                    'code' => $data['code'] ?? $city->code,
                    'name' => $data['name'] ?? $city->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $city->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $city->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $city->is_default,
                    'draft' => $data['draft'] ?? $city->draft,
                    'drafted_at' => $data['draft'] == 1 ? now() : $city->drafted_at,
                    'is_active' => $data['is_active'] ?? $city->is_active,
                    'country_id' => $data['country_id'] ?? $city->country_id,
                    'state_id' => $data['state_id'] ?? $city->state_id,
                    'description' => $data['description'] ?? $city->description,
                ]);
                // Log activity for update
                ActivityLogger::log('City Updated', 'City', 'City', $city->id, [
                    'name' => $city->name ?? '',
                    'country_id' => $city->country_id ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating City: ', [
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
