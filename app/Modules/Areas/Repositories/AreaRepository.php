<?php

namespace App\Modules\Areas\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AreaRepository
{


    public function getSummaryData()
    {
        $areas = Area::withTrashed()->get(); // Load all records including soft-deleted

        $totalArea = $areas->count();

        return [
            'totalArea' => $totalArea,
        ];
    }
    public function all()
    {
        return Area::cursor(); // Load all records
    }

    public function store(array $data): ?Area
    {
        try {
            DB::beginTransaction();

            // Create the Area record in the database
            $area = Area::create($data);

            // Log activity
            ActivityLogger::log('Area Add', 'Areas', 'Area', $area->id, [
                'name' => $country->name ?? '',
            ]);

            DB::commit();

            return $area;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Area: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Area $area, array $data): ?Area
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $area->update($data);
            // Log activity for update
            ActivityLogger::log('Area Updated', 'Areas', 'Area', $area->id, [
                'name' => $area->name
            ]);

            DB::commit();
            return $area;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating state: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    public function delete(Area $area): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $area->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Area Deleted', 'Areas', 'Area', $area->id, [
                'name' => $area->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting state: ' . $e->getMessage(), [
                'state_id' => $area->id,
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
        $area = Area::leftJoin('states', 'areas.state_id', '=', 'states.id')
            ->leftJoin('countries', 'areas.country_id', '=', 'countries.id')
            ->leftJoin('cities', 'areas.city_id', '=', 'cities.id')
            ->select(['areas.id as id', 'cities.name as city_name', 'states.name as state_name', 'countries.name as country_name'])
            ->first();
        return $area;
    }
}
