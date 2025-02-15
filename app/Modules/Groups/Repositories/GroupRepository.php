<?php

namespace App\Modules\Groups\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\Groups\Models\Group;
use App\Modules\Items\Models\ItemGroup;
use App\Modules\ProductUnits\Models\ProductUnit;
use App\Modules\Sample\Models\SampleCategory;
use App\Modules\Sample\Models\SampleReceiving;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use App\Modules\Tags\Models\Tag;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class GroupRepository
{
    public function getSummaryData()
    {
        $groups = Group::withTrashed()->get(); // Load all records including soft-deleted

        $totalGroups = $groups->count();

        return [
            'totalGroups' => $totalGroups,
        ];
    }
    public function all()
    {
        return Group::cursor(); // Load all records
    }

    public function store(array $data): ?Group
    {
        try {
            DB::beginTransaction();

            // Create the Group record in the database
            $store = Group::create($data);

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
            Log::error('Error in storing Group: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Group $group, array $data): ?Group
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $group->update($data);

            DB::commit();
            return $group;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Group: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Group $group): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $group->delete();
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
                'state_id' => $group->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Group::find($id);
    }
    public function getData($id)
    {
        $store = Group::where('id', $id)->first();
        return $store;
    }
    public function checkExist($id)
    {
        $exist = SampleReceiving::where('section', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
