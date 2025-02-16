<?php

namespace App\Modules\Colors\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Colors\Models\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ColorRepository
{
    public function getSummaryData()
    {
        $colors = Color::withTrashed()->get(); // Load all records including soft-deleted

        $totalColors = $colors->count();

        return [
            'totalColors' => $totalColors,
        ];
    }
    public function all()
    {
        return Color::cursor(); // Load all records
    }

    public function store(array $data): ?Color
    {
        try {
            DB::beginTransaction();

            // Create the Color record in the database
            $store = Color::create($data);

            // Log activity
//            ActivityLogger::log('Country Add', 'Country', 'Country', $country->id, [
//                'name' => $country->name ?? '',
//                'code' => $country->code ?? ''
//            ]);

            DB::commit();

            return $store;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Color: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Color $brand, array $data): ?Color
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $brand->update($data);

            DB::commit();
            return $brand;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Color: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Color $brand): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $brand->delete();
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
            Log::error('Error deleting Sample Category: ' . $e->getMessage(), [
                'state_id' => $brand->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Color::find($id);
    }
    public function getData($id)
    {
        $store = Color::where('id', $id)->first();
        return $store;
    }
    public function checkExist($id)
    {
        $exist = Color::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
