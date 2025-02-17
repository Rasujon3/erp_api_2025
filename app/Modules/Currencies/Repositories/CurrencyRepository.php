<?php

namespace App\Modules\Currencies\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use App\Modules\Currencies\Models\Currency;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrencyRepository
{


    public function getSummaryData()
    {
        $currencies = Currency::withTrashed()->get(); // Load all records including soft-deleted

        $totalCurrency = $currencies->count();

        return [
            'totalCurrency' => $totalCurrency,
        ];
    }
    public function all()
    {
        return Currency::cursor(); // Load all records
    }

    public function store(array $data): ?Currency
    {
        try {
            DB::beginTransaction();

            // Create the Currency record in the database
            $currency = Currency::create($data);

            // Log activity
            ActivityLogger::log('Currency Add', 'Currencies', 'Currency', $currency->id, [
                'name' => $currency->name ?? '',
            ]);

            DB::commit();

            return $currency;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Currency: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Currency $currency, array $data): ?Currency
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $currency->update($data);
            // Log activity for update
            ActivityLogger::log('Currency Updated', 'Currencies', 'Currency', $currency->id, [
                'name' => $currency->name
            ]);

            DB::commit();
            return $currency;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating currency: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    public function delete(Currency $currency): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $currency->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Currency Deleted', 'Currencies', 'Currency', $currency->id, [
                'name' => $currency->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting currency: ' . $e->getMessage(), [
                'state_id' => $currency->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Currency::find($id);
    }
    public function getData($id)
    {
        $currency = Currency::where('id', $id)
            ->select(['id', 'name', 'description'])
            ->first();
        return $currency;
    }
}
