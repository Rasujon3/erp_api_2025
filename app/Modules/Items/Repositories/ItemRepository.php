<?php

namespace App\Modules\Items\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\Items\Models\Item;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ItemRepository
{


    public function getSummaryData()
    {
        $items = Item::withTrashed()->get(); // Load all records including soft-deleted

        $totalItem = $items->count();

        return [
            'totalItem' => $totalItem,
        ];
    }
    public function all()
    {
        return Item::cursor(); // Load all records
    }

    public function store(array $data): ?Item
    {
        try {
            DB::beginTransaction();

            // Create the Item record in the database
            $store = Item::create($data);

            // Log activity
            ActivityLogger::log('Item Add', 'Items', 'Item', $store->id, [
                'name' => $store->title ?? '',
            ]);

            DB::commit();

            return $store;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Item: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Item $item, array $data): ?Item
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $item->update($data);
            // Log activity for update
            ActivityLogger::log('Item Updated', 'Items', 'Item', $item->id, [
                'name' => $item->title ?? '',
            ]);

            DB::commit();
            return $item;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Tax Rate: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    public function delete(Item $item): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $item->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Item Deleted', 'Items', 'Item', $item->id, [
                'name' => $item->title ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Tax Rate: ' . $e->getMessage(), [
                'state_id' => $item->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Item::find($id);
    }
    public function getData($id)
    {
        $store = Item::leftJoin('tax_rates', 'items.tax_1_id', '=', 'tax_rates.id')
            ->leftJoin('tax_rates as tax_2', 'items.tax_2_id', '=', 'tax_2.id')
            ->leftJoin('item_groups', 'items.item_group_id', '=', 'item_groups.id')
            ->where('items.id', $id)
            ->select('items.*', 'tax_rates.name as tax_1_name', 'tax_2.name as tax_2_name', 'item_groups.name as item_group_name')
            ->first();
        return $store;
    }
}
