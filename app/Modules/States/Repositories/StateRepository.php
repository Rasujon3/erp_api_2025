<?php

namespace App\Modules\States\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StateRepository
{
    public function all()
    {
        $list = State::cursor(); // Load all records without soft-deleted
        $countries = State::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $countries->where('draft', true)->count();
        $totalInactive = $countries->where('is_active', false)->count();
        $totalActive = $countries->where('is_active', true)->count();
        $totalDeleted = $countries->whereNotNull('deleted_at')->count();
        $totalUpdated = $countries->whereNotNull('updated_at')->count();

        // Ensure totalCountries is the sum of totalDraft + totalInactive + totalActive
        $totalStates = $totalDraft + $totalInactive + $totalActive + $totalDeleted;
        return [
            'totalStates' => $totalStates,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }
    public function store(array $data): ?State
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Create the State record in the database
            $state = State::create($data);

            // Log activity
            ActivityLogger::log('State Add', 'States', 'State', $state->id, [
                'name' => $state->name ?? '',
                'country_id' => $state->country_id ?? ''
            ]);

            DB::commit();

            return $state;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing State: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(State $state, array $data): ?State
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Perform the update
            $state->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($state);
            } else {
                // Log activity for update
                ActivityLogger::log('State Updated', 'States', 'State', $state->id, [
                    'name' => $state->name ?? '',
                    'country_id' => $state->country_id ?? ''
                ]);
            }

            DB::commit();
            return $state;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating state: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(State $state): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $state->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('State Deleted', 'States', 'State', $state->id, [
                'name' => $state->name ?? '',
                'country_id' => $state->country_id ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting state: ' , [
                'state_id' => $state->id,
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
        return State::find($id);
    }
    public function getData($id)
    {
        $state = State::leftJoin('countries', 'states.country_id', '=', 'countries.id')
            ->where('states.id', $id)
            ->select('states.*', 'countries.name as country_name')
            ->first();
        return $state;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->states as $data) {
                $state = State::find($data['id']);

                if (!$state) {
                    continue; // Skip if state not found
                }

                // Update state details
                $state->update([
                    'name' => $data['name'] ?? $state->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $state->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $state->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $state->is_default,
                    'draft' => $data['draft'] ?? $state->draft,
                    'drafted_at' => $data['draft'] == 1 ? now() : $state->drafted_at,
                    'is_active' => $data['is_active'] ?? $state->is_active,
                    'country_id' => $data['country_id'] ?? $state->country_id,
                    'description' => $data['description'] ?? $state->description,
                ]);
                // Log activity for update
                ActivityLogger::log('State Updated', 'State', 'State', $state->id, [
                    'name' => $state->name ?? '',
                    'country_id' => $state->country_id ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk updating State: ', [
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
        $exist = City::where('state_id', $id)->whereNull('deleted_at')->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
