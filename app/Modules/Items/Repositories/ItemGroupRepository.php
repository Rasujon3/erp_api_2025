<?php

namespace App\Modules\Items\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\Items\Models\ItemGroup;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ItemGroupRepository
{


    public function getSummaryData()
    {
        $itemGroups = ItemGroup::withTrashed()->get(); // Load all records including soft-deleted

        $totalItemGroup = $itemGroups->count();

        return [
            'totalItemGroup' => $totalItemGroup,
        ];
    }
    public function all()
    {
        return ItemGroup::cursor(); // Load all records
    }

    public function store(array $data): ?ItemGroup
    {
        try {
            DB::beginTransaction();

            // Create the ItemGroup record in the database
            $store = ItemGroup::create($data);

            // Log activity
            ActivityLogger::log('ItemGroup Add', 'ItemGroups', 'ItemGroup', $store->id, [
                'name' => $store->name ?? '',
            ]);

            DB::commit();

            return $store;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing ItemGroup: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(ItemGroup $itemGroup, array $data): ?ItemGroup
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $itemGroup->update($data);
            // Log activity for update
            ActivityLogger::log('ItemGroup Updated', 'ItemGroups', 'ItemGroup', $itemGroup->id, [
                'name' => $itemGroup->name
            ]);

            DB::commit();
            return $itemGroup;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Item Group: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(ItemGroup $itemGroup): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $itemGroup->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('ItemGroup Deleted', 'ItemGroups', 'ItemGroup', $itemGroup->id, [
                'name' => $country->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Tax Rate: ' . $e->getMessage(), [
                'state_id' => $itemGroup->id,
                'trace' => $e->getTraceAsString()
            ]);
            // Throw the error explicitly to be caught in the controller
            throw new Exception($e->getMessage());
        }
    }


    public function find($id)
    {
        return ItemGroup::find($id);
    }
    public function getData($id)
    {
        $store = ItemGroup::where('id', $id)->first();
        return $store;
    }
}
