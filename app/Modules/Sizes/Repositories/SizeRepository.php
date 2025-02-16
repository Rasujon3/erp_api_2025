<?php

namespace App\Modules\Sizes\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Sizes\Models\Size;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SizeRepository
{
    public function getSummaryData()
    {
        $sizes = Size::withTrashed()->get(); // Load all records including soft-deleted

        $totalSizes = $sizes->count();

        return [
            'totalSizes' => $totalSizes,
        ];
    }
    public function all()
    {
        return Size::cursor(); // Load all records
    }

    public function store(array $data): ?Size
    {
        try {
            DB::beginTransaction();

            // Create the Size record in the database
            $store = Size::create($data);

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
            Log::error('Error in storing Size: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Size $size, array $data): ?Size
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $size->update($data);

            DB::commit();
            return $size;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Size: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Size $size): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $size->delete();
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
                'state_id' => $size->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Size::find($id);
    }
    public function getData($id)
    {
        $store = Size::where('id', $id)->first();
        return $store;
    }
    public function checkExist($id)
    {
        $exist = Size::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
