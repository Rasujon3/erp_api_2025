<?php

namespace App\Modules\Brands\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\Brands\Models\Brand;
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

class BrandRepository
{
    public function getSummaryData()
    {
        $brands = Brand::withTrashed()->get(); // Load all records including soft-deleted

        $totalBrands = $brands->count();

        return [
            'totalBrands' => $totalBrands,
        ];
    }
    public function all()
    {
        return Brand::cursor(); // Load all records
    }

    public function store(array $data): ?Brand
    {
        try {
            DB::beginTransaction();

            // Create the Brand record in the database
            $store = Brand::create($data);

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
            Log::error('Error in storing Brand: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Brand $brand, array $data): ?Brand
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
            Log::error('Error updating Brand: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Brand $brand): bool
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
        return Brand::find($id);
    }
    public function getData($id)
    {
        $store = Brand::where('id', $id)->first();
        return $store;
    }
    public function checkExist($id)
    {
        $exist = Brand::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
