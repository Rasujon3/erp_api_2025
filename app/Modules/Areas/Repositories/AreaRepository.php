<?php

namespace App\Modules\Areas\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AreaRepository
{
    public function all()
    {
        $list = Area::cursor(); // Load all records without soft-deleted
        $areas = Area::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $areas->where('draft', true)->count();
        $totalInactive = $areas->where('is_active', false)->count();
        $totalActive = $areas->where('is_active', true)->count();
        $totalDeleted = $areas->whereNotNull('deleted_at')->count();
        $totalUpdated = $areas->whereNotNull('updated_at')->count();

        // Ensure totalCountries is the sum of totalDraft + totalInactive + totalActive
        $totalAreas = $totalDraft + $totalInactive + $totalActive + $totalDeleted;
        return [
            'totalAreas' => $totalAreas,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }
    public function store(array $data): ?Area
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Create the Area record in the database
            $area = Area::create($data);

            // Log activity
            ActivityLogger::log('Area Add', 'Area', 'Area', $area->id, [
                'name' => $area->name ?? '',
                'country_id' => $area->country_id ?? '',
                'state_id' => $area->state_id ?? '',
                'city_id' => $area->city_id ?? ''
            ]);

            DB::commit();

            return $area;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Area: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Area $area, array $data): ?Area
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Perform the update
            $area->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($area);
            } else {
                // Log activity for update
                ActivityLogger::log('Area Updated', 'Area', 'Area', $area->id, [
                    'name' => $area->name ?? '',
                    'country_id' => $area->country_id ?? '',
                    'state_id' => $area->state_id ?? '',
                    'city_id' => $area->city_id ?? '',
                ]);
            }

            DB::commit();
            return $area;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Area: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Area $area): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $area->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Area Deleted', 'Area', 'Area', $area->id, [
                'name' => $area->name ?? '',
                'country_id' => $area->country_id ?? '',
                'state_id' => $area->state_id ?? '',
                'city_id' => $area->city_id ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Area: ' , [
                'state_id' => $area->id,
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
        return Area::find($id);
    }
    public function getData($id)
    {
        $area = Area::leftJoin('countries', 'countries.id', '=', 'areas.country_id')
            ->leftJoin('states', 'states.id', '=', 'areas.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'areas.city_id')
            ->where('areas.id', $id)
            ->select(
                'areas.*',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name'
            )
            ->first();
        return $area;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->areas as $data) {
                $area = Area::find($data['id']);

                if (!$area) {
                    continue; // Skip if city not found
                }

                // Update state details
                $area->update([
                    'code' => $data['code'] ?? $area->code,
                    'name' => $data['name'] ?? $area->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $area->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $area->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $area->is_default,
                    'draft' => $data['draft'] ?? $area->draft,
                    'drafted_at' => $data['draft'] == 1 ? now() : $area->drafted_at,
                    'is_active' => $data['is_active'] ?? $area->is_active,
                    'country_id' => $data['country_id'] ?? $area->country_id,
                    'state_id' => $data['state_id'] ?? $area->state_id,
                    '"city_id"' => $data['city_id'] ?? $area->city_id,
                    'description' => $data['description'] ?? $area->description,
                ]);
                // Log activity for update
                ActivityLogger::log('Area Updated', 'Area', 'Area', $area->id, [
                    'name' => $area->name ?? '',
                    'country_id' => $area->country_id ?? '',
                    'state_id' => $area->state_id ?? '',
                    'city_id' => $area->city_id ?? '',
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Area: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
