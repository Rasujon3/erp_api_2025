<?php

namespace App\Modules\City\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CityRepository
{


    public function getSummaryData()
    {
        # $states = City::withTrashed()->get(); // Load all records including soft-deleted

        $totalCity = City::get()->count();

        return [
            'totalCity' => $totalCity,
        ];
    }
    public function all()
    {
        return City::cursor(); // Load all records
    }

    public function store(array $data): ?City
    {
        try {
            DB::beginTransaction();

            // Create the City record in the database
            $state = City::create($data);

            // Log activity
//            ActivityLogger::log('Country Add', 'Country', 'Country', $country->id, [
//                'name' => $country->name ?? '',
//                'code' => $country->code ?? ''
//            ]);

            DB::commit();

            return $state;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing City: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(City $city, array $data): ?City
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $city->update($data);

            DB::commit();
            return $city;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating state: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    public function delete(City $city): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $city->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
//            ActivityLogger::log('Country Deleted', 'Country', 'Country', $country->id, [
//                'name' => $country->name ?? '',
//                'code' => $country->code ?? '',
//            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting state: ' . $e->getMessage(), [
                'state_id' => $city->id,
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
        $city = City::leftJoin('states', 'cities.state_id', '=', 'states.id')
            ->leftJoin('countries', 'states.country_id', '=', 'countries.id')
            ->select(['cities.id as id', 'cities.name as city_name', 'states.name as state_name', 'countries.name as country_name'])
            ->first();
        return $city;
    }
}
