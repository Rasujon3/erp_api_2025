<?php

namespace App\Modules\Warehouses\Repositories;

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
use App\Modules\Warehouses\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WarehouseRepository
{
    public function getSummaryData()
    {
        $warehouses = Warehouse::withTrashed()->get(); // Load all records including soft-deleted

        $totalWarehouses = $warehouses->count();

        return [
            'totalWarehouses' => $totalWarehouses,
        ];
    }
    public function all()
    {
        return Warehouse::cursor(); // Load all records
    }

    public function store(array $data): ?Warehouse
    {
        try {
            DB::beginTransaction();

            // Create the Warehouse record in the database
            $store = Warehouse::create($data);

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
            Log::error('Error in storing Warehouse: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Warehouse $warehouse, array $data): ?Warehouse
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $warehouse->update($data);

            DB::commit();
            return $warehouse;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Warehouse: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Warehouse $warehouse): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $warehouse->delete();
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
                'state_id' => $warehouse->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Warehouse::find($id);
    }
    public function getData($id)
    {
        $warehouse = Warehouse::leftJoin('divisions', 'warehouses.division_id', '=', 'divisions.id')
            ->where('warehouses.id', $id)
            ->select(['warehouses.*', 'divisions.name as division_name'])
            ->first();
        return $warehouse;
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
