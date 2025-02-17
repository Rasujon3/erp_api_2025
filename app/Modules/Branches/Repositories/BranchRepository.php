<?php

namespace App\Modules\Branches\Repositories;

use App\Modules\Admin\Models\Country;
use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Branches\Models\Branch;
use App\Modules\City\Models\City;
use App\Modules\Currencies\Models\Currency;
use App\Modules\States\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BranchRepository
{


    public function getSummaryData()
    {
        $branches = Branch::withTrashed()->get(); // Load all records including soft-deleted

        $totalBranches = $branches->count();
        $countries = Country::select('id', 'name')->get();
        $currencies = Currency::select('id', 'name')->get();

        return [
            'totalBranches' => $totalBranches,
            'countries' => $countries,
            'currencies' => $currencies,
        ];
    }
    public function all()
    {
        return Branch::cursor(); // Load all records
    }

    public function store(array $data): ?Branch
    {
        try {
            DB::beginTransaction();

            // Create the Branch record in the database
            $branch = Branch::create($data);

            // Log activity
            ActivityLogger::log('Branch Add', 'Branches', 'Branch', $branch->id, [
                'name' => $branch->name ?? '',
            ]);

            DB::commit();

            return $branch;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Branch: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Branch $branch, array $data): ?Branch
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $branch->update($data);
            // Log activity for update
            ActivityLogger::log('Branch Updated', 'Branches', 'Branch', $branch->id, [
                'name' => $branch->name
            ]);

            DB::commit();
            return $branch;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating state: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    public function delete(Branch $branch): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $branch->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Branch Deleted', 'Branches', 'Branch', $branch->id, [
                'name' => $branch->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting state: ' . $e->getMessage(), [
                'state_id' => $branch->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Branch::find($id);
    }
    public function getData($id)
    {
        $branch = Branch::leftJoin('countries', 'branches.country_id', '=', 'countries.id')
            ->leftJoin('currencies', 'branches.currency_id', '=', 'currencies.id')
            ->where('branches.id', $id)
            ->select(['branches.id as id', 'branches.*', 'currencies.name as currency_name', 'countries.name as country_name'])
            ->first();
        return $branch;
    }
}
