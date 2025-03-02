<?php

namespace App\Modules\Bonuses\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Bonuses\Models\Bonus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BonusRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $bonuses = Bonus::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $bonuses->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $bonuses->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $bonuses->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $bonuses->whereNotNull('deleted_at')->count();
        $totalUpdated = $bonuses->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalBonuses = $bonuses->whereNull('deleted_at')->count();

        return [
            'totalBonuses' => $totalBonuses,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }

    public function list($request)
    {
        $query = Bonus::withTrashed()
            ->leftJoin('employees', 'bonuses.employee_id', '=', 'employees.id')
            ->leftJoin('bonus_types', 'bonuses.bonus_type_id', '=', 'bonus_types.id')
            ->select('bonuses.*', 'employees.name as employee_name', 'bonus_types.name as bonus_type_name');

        if ($request->has('draft')) {
            $query->where('bonuses.draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('bonuses.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('bonuses.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('bonuses.deleted_at');
            } else {
                $query->whereNull('bonuses.deleted_at');
            }
        } else {
            $query->whereNull('bonuses.deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('bonuses.updated_at');
            } else {
                $query->whereNull('bonuses.updated_at');
            }
        }
        if ($request->has('country_id')) {
            $query->where('bonuses.country_id', $request->input('country_id'));
        }
        if ($request->has('state_id')) {
            $query->where('bonuses.state_id', $request->input('state_id'));
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?Bonus
    {
        DB::beginTransaction();
        try {
            /*
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Create the Bonus record in the database
            $bonus = Bonus::create($data);

            // Log activity
            ActivityLogger::log('Bonus Add', 'Bonus', 'Bonus', $bonus->id, [
                'employee_id' => $bonus->employee_id ?? '',
                'amount' => $bonus->amount ?? '',
                'bonus_type_id' => $bonus->bonus_type_id ?? ''
            ]);

            DB::commit();

            return $bonus;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Bonus: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Bonus $bonus, array $data): ?Bonus
    {
        DB::beginTransaction();
        try {
            /*
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Perform the update
            $bonus->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($bonus);
            } else {
                // Log activity for update
                ActivityLogger::log('Bonus Updated', 'Bonus', 'Bonus', $bonus->id, [
                    'employee_id' => $bonus->employee_id ?? '',
                    'amount' => $bonus->amount ?? '',
                    'bonus_type_id' => $bonus->bonus_type_id ?? ''
                ]);
            }

            DB::commit();
            return $bonus;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Bonus: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Bonus $bonus): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $bonus->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Bonus Deleted', 'Bonus', 'Bonus', $bonus->id, [
                'employee_id' => $bonus->employee_id ?? '',
                'amount' => $bonus->amount ?? '',
                'bonus_type_id' => $bonus->bonus_type_id ?? ''
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Bonus: ' , [
                'state_id' => $bonus->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Bonus::find($id);
    }
    public function getData($id)
    {
        $bonus = Bonus::leftJoin('employees', 'bonuses.employee_id', '=', 'employees.id')
            ->leftJoin('bonus_types', 'bonuses.bonus_type_id', '=', 'bonus_types.id')
            ->where('bonuses.id', $id)
            ->select('bonuses.*', 'employees.name as employee_name', 'bonus_types.name as bonus_type_name')
            ->first();
        return $bonus;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->bonuses as $data) {
                $bonus = Bonus::find($data['id']);

                if (!$bonus) {
                    continue; // Skip if not found
                }

                // Update state details
                $bonus->update([
                    'employee_id' => $data['employee_id'] ?? $bonus->employee_id,
                    'amount' => $data['amount'] ?? $bonus->amount,
                    'bonus_type_id' => $data['bonus_type_id'] ?? $bonus->bonus_type_id,
                    'date' => $data['date'] ?? $bonus->date,
                    'posted' => $data['posted'] ?? $bonus->posted,
                    'description' => $data['description'] ?? $bonus->description,
                ]);
                // Log activity for update
                ActivityLogger::log('Bonus Updated', 'Bonus', 'Bonus', $bonus->id, [
                    'employee_id' => $bonus->employee_id ?? '',
                    'amount' => $bonus->amount ?? '',
                    'bonus_type_id' => $bonus->bonus_type_id ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Bonus: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function checkExist($id): bool
    {
        $existOnArea = Area::where('city_id', $id)->whereNull('deleted_at')->exists();
        if ($existOnArea) {
            return true;
        }
        return false;
    }
}
