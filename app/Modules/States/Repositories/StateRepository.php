<?php

namespace App\Modules\States\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StateRepository
{


    public function getSummaryData()
    {
        # $states = State::withTrashed()->get(); // Load all records including soft-deleted

        $totalState = State::get()->count();

        return [
            'totalState' => $totalState,
        ];
    }
    public function all()
    {
        return State::cursor(); // Load all records
    }

    public function store(array $data): ?State
    {
        try {
            DB::beginTransaction();

            // Create the State record in the database
            $state = State::create($data);

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
            Log::error('Error in storing State: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(State $state, array $data): ?State
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $state->update($data);

            DB::commit();
            return $state;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating state: ' . $e->getMessage(), [
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


    public function delete(State $state): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $state->delete();
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
                'state_id' => $state->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return State::find($id);
    }
    public function getData($id)
    {
        $state = State::leftJoin('countries', 'states.country_id', '=', 'countries.id')
            ->where('states.id', $id)
            ->select('states.name as state_name', 'states.description as state_description', 'countries.name as country_name')
            ->first();
        return $state;
    }
}
