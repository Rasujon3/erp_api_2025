<?php

namespace App\Modules\Tags\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
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

class TagRepository
{


    public function getSummaryData()
    {
        # $states = Store::withTrashed()->get(); // Load all records including soft-deleted

        $totalTags = Tag::get()->count();

        return [
            'totalTags' => $totalTags,
        ];
    }
    public function all()
    {
        return Tag::cursor(); // Load all records
    }

    public function store(array $data): ?Tag
    {
        try {
            DB::beginTransaction();

            // Create the Tag record in the database
            $store = Tag::create($data);

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
            Log::error('Error in storing Tag: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Tag $tag, array $data): ?Tag
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $tag->update($data);

            DB::commit();
            return $tag;
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
    public function delete(Tag $tag): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $tag->delete();
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
                'state_id' => $tag->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Tag::find($id);
    }
    public function getData($id)
    {
        $store = Tag::where('id', $id)->first();
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
