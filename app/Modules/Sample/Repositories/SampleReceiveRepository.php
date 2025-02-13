<?php

namespace App\Modules\Sample\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\Items\Models\Item;
use App\Modules\Sample\Models\SampleReceiving;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SampleReceiveRepository
{


    public function getSummaryData()
    {
        # $states = Store::withTrashed()->get(); // Load all records including soft-deleted

        $totalReceiving = SampleReceiving::get()->count();
        $configDb = config('database.connections.mysql.database');
        $presentId = DB::select("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$configDb, 'sample_receiving']);
        $nextId = $presentId[0]->AUTO_INCREMENT ?: 1;

        return [
            'totalReceiving' => $totalReceiving,
            'nextId' => $nextId,
        ];
    }
    public function all()
    {
        return SampleReceiving::cursor(); // Load all records
    }

    public function store(array $data): ?SampleReceiving
    {
        try {
            DB::beginTransaction();

            // Create the SampleReceiving record in the database
            $store = SampleReceiving::create($data);

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
            Log::error('Error in storing Sample Receiving: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(SampleReceiving $sampleReceiving, array $data): ?SampleReceiving
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $sampleReceiving->update($data);

            DB::commit();
            return $sampleReceiving;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Sample Receiving: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    public function delete(SampleReceiving $sampleReceiving): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $sampleReceiving->delete();
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
                'state_id' => $sampleReceiving->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return SampleReceiving::find($id);
    }
    public function getData($id)
    {
        $receivingData = SampleReceiving::leftJoin('sample_categories', 'sample_receiving.section', '=', 'sample_categories.id')
            ->leftJoin('employees as delivered_by_name', 'sample_receiving.delivered_by', '=', 'delivered_by_name.id')
            ->leftJoin('employees as received_by_name', 'sample_receiving.received_by', '=', 'received_by_name.id')
            ->where('sample_receiving.id', $id)
            ->select('sample_receiving.*', 'delivered_by_name.name as delivered_by_name',
                'received_by_name.name as received_by_name', 'sample_categories.name as sample_category_name')
            ->first();
        return $receivingData;
    }
}
