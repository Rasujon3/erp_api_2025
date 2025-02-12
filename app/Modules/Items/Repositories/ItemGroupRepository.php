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
        # $states = Store::withTrashed()->get(); // Load all records including soft-deleted

        $totalItemGroup = ItemGroup::get()->count();

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
//            ActivityLogger::log('Country Add', 'Country', 'Country', $country->id, [
//                'name' => $country->name ?? '',
//                'code' => $country->code ?? ''
//            ]);

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
//            ActivityLogger::log('Country Deleted', 'Country', 'Country', $country->id, [
//                'name' => $country->name ?? '',
//                'code' => $country->code ?? '',
//            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Tax Rate: ' . $e->getMessage(), [
                'state_id' => $itemGroup->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
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
