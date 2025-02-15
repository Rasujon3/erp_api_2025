<?php

namespace App\Modules\Divisions\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\Divisions\Models\Division;
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

class DivisionRepository
{
    public function getSummaryData()
    {
        $divisions = Division::withTrashed()->get(); // Load all records including soft-deleted

        $totalDivisions = $divisions->count();

        return [
            'totalDivisions' => $totalDivisions,
        ];
    }
    public function all()
    {
        return Division::cursor(); // Load all records
    }

    public function store(array $data): ?Division
    {
        try {
            DB::beginTransaction();

            // Create the Division record in the database
            $store = Division::create($data);

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
            Log::error('Error in storing Division: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Division $group, array $data): ?Division
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
            Log::error('Error updating Division: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Division $group): bool
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
        return Division::find($id);
    }
    public function getData($id)
    {
        $division = Division::leftJoin('groups', 'divisions.group_id', '=', 'groups.id')
            ->where('divisions.id', $id)
            ->select(['divisions.*', 'groups.name as group_name'])
            ->first();
        return $division;
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
