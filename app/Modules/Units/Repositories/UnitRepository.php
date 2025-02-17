<?php

namespace App\Modules\Units\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Units\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UnitRepository
{
    public function getSummaryData()
    {
        $units = Unit::withTrashed()->get(); // Load all records including soft-deleted

        $totalUnits = $units->count();

        return [
            'totalUnits' => $totalUnits,
        ];
    }
    public function all()
    {
        return Unit::cursor(); // Load all records
    }

    public function store(array $data): ?Unit
    {
        try {
            DB::beginTransaction();

            // Create the Unit record in the database
            $unit = Unit::create($data);

            // Log activity
            ActivityLogger::log('Unit Add', 'Units', 'Unit', $unit->id, [
                'name' => $unit->name ?? '',
                'description' => $unit->description ?? ''
            ]);

            DB::commit();

            return $unit;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Unit: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Unit $unit, array $data): ?Unit
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $unit->update($data);
            // Log activity for update
            ActivityLogger::log('Unit Updated', 'Units', 'Unit', $unit->id, [
                'name' => $unit->name
            ]);

            DB::commit();
            return $unit;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Unit: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Unit $unit): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $unit->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Unit Deleted', 'Units', 'Unit', $unit->id, [
                'name' => $unit->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Unit: ' . $e->getMessage(), [
                'unit_id' => $unit->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Unit::find($id);
    }
    public function getData($id)
    {
        $store = Unit::where('id', $id)->first();
        return $store;
    }
    public function checkExist($id)
    {
        $exist = Unit::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
