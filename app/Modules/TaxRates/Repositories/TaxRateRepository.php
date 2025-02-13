<?php

namespace App\Modules\TaxRates\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\City\Models\City;
use App\Modules\States\Models\State;
use App\Modules\Stores\Models\Store;
use App\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TaxRateRepository
{


    public function getSummaryData()
    {
        # $states = Store::withTrashed()->get(); // Load all records including soft-deleted

        $totalTaxRate = TaxRate::get()->count();

        return [
            'totalTaxRate' => $totalTaxRate,
        ];
    }
    public function all()
    {
        return TaxRate::cursor(); // Load all records
    }

    public function store(array $data): ?TaxRate
    {
        try {
            DB::beginTransaction();

            // Create the TaxRate record in the database
            $store = TaxRate::create($data);

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
            Log::error('Error in storing TaxRate: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(TaxRate $taxRate, array $data): ?TaxRate
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $taxRate->update($data);

            DB::commit();
            return $taxRate;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Tax Rate: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(TaxRate $taxRate): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $taxRate->delete();
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
                'state_id' => $taxRate->id,
                'trace' => $e->getTraceAsString()
            ]);

            // Throw the error explicitly to be caught in the controller
            throw new Exception($e->getMessage());
        }
    }


    public function find($id)
    {
        return TaxRate::find($id);
    }
    public function getData($id)
    {
        $store = TaxRate::where('id', $id)->first();
        return $store;
    }
}
