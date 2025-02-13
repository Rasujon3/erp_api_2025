<?php

namespace App\Modules\Sample\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\Items\Models\ItemGroup;
use App\Modules\Sample\Models\SampleCategory;
use App\Modules\Sample\Models\SampleReceiving;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SampleCategoryRepository
{


    public function getSummaryData()
    {
        # $states = Store::withTrashed()->get(); // Load all records including soft-deleted

        $totalSampleCategory = SampleCategory::get()->count();

        return [
            'totalSampleCategory' => $totalSampleCategory,
        ];
    }
    public function all()
    {
        return SampleCategory::cursor(); // Load all records
    }

    public function store(array $data): ?SampleCategory
    {
        try {
            DB::beginTransaction();

            // Create the SampleCategory record in the database
            $store = SampleCategory::create($data);

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
            Log::error('Error in storing SampleCategory: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(SampleCategory $sampleCategory, array $data): ?SampleCategory
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $sampleCategory->update($data);

            DB::commit();
            return $sampleCategory;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Sample Category: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(SampleCategory $sampleCategory): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $sampleCategory->delete();
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
                'state_id' => $sampleCategory->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return SampleCategory::find($id);
    }
    public function getData($id)
    {
        $store = SampleCategory::where('id', $id)->first();
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
