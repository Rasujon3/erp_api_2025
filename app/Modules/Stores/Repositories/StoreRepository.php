<?php

namespace App\Modules\Stores\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreRepository
{


    public function getSummaryData()
    {
        $stores = Store::withTrashed()->get(); // Load all records including soft-deleted

        $totalStore = $stores->count();

        return [
            'totalStore' => $totalStore,
        ];
    }
    public function all()
    {
        return Store::cursor(); // Load all records
    }

    public function store(array $data): ?Store
    {
        try {
            DB::beginTransaction();

            // Create the Store record in the database
            $store = Store::create($data);

            // Log activity
            ActivityLogger::log('Store Add', 'Stores', 'Store', $store->id, [
                'name' => $store->name ?? '',
            ]);

            DB::commit();

            return $store;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Store $store, array $data): ?Store
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $store->update($data);
            // Log activity for update
            ActivityLogger::log('Store Updated', 'Stores', 'Store', $store->id, [
                'name' => $store->name
            ]);

            DB::commit();
            return $store;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    public function delete(Store $store): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $store->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Store Deleted', 'Stores', 'Store', $store->id, [
                'name' => $store->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting state: ' . $e->getMessage(), [
                'state_id' => $store->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Store::find($id);
    }
    public function getData($id)
    {
        $store = Store::where('id', $id)->first();
        return $store;
    }
}
