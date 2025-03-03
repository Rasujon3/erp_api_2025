<?php

namespace App\Modules\Allowances\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Allowances\Models\Allowance;
use App\Modules\Areas\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AllowanceRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $allowances = Allowance::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $allowances->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $allowances->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $allowances->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $allowances->whereNotNull('deleted_at')->count();
        $totalUpdated = $allowances->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalLoans = $allowances->whereNull('deleted_at')->count();

        return [
            'totalLoans' => $totalLoans,
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
        $query = Allowance::withTrashed()
            ->leftJoin('employees as ei', 'allowances.employee_id', '=', 'ei.id')
            ->leftJoin('allowance_types as at', 'allowances.allowance_type_id', '=', 'at.id')
            ->select('allowances.*', 'ei.name as employee_name', 'at.name as allowance_types_name');

        if ($request->has('draft')) {
            $query->where('allowances.draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('allowances.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('allowances.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('allowances.deleted_at');
            } else {
                $query->whereNull('allowances.deleted_at');
            }
        } else {
            $query->whereNull('allowances.deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('allowances.updated_at');
            } else {
                $query->whereNull('allowances.updated_at');
            }
        }
        if ($request->has('country_id')) {
            $query->where('allowances.country_id', $request->input('country_id'));
        }
        if ($request->has('state_id')) {
            $query->where('allowances.state_id', $request->input('state_id'));
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?Allowance
    {
        DB::beginTransaction();
        try {
            /*
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Create the Allowance record in the database
            $allowance = Allowance::create($data);

            // Log activity
            ActivityLogger::log('Allowance Add', 'Allowance', 'Allowance', $allowance->id, [
                'employee_id' => $allowance->employee_id ?? '',
                'amount' => $allowance->amount ?? ''
            ]);

            DB::commit();

            return $allowance;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Allowance: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Allowance $allowance, array $data): ?Allowance
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
            $allowance->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($allowance);
            } else {
                // Log activity for update
                ActivityLogger::log('Allowance Updated', 'Allowance', 'Allowance', $allowance->id, [
                    'employee_id' => $allowance->employee_id ?? '',
                    'amount' => $allowance->amount ?? ''
                ]);
            }

            DB::commit();
            return $allowance;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Allowance: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Allowance $allowance): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $allowance->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Allowance Deleted', 'Allowance', 'Allowance', $allowance->id, [
                'employee_id' => $allowance->employee_id ?? '',
                'amount' => $allowance->amount ?? ''
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Allowance: ' , [
                'state_id' => $allowance->id,
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
        return Allowance::find($id);
    }
    public function getData($id)
    {
        $allowance = Allowance::leftJoin('employees as ei', 'allowances.employee_id', '=', 'ei.id')
            ->leftJoin('allowance_types as at', 'allowances.allowance_type_id', '=', 'at.id')
            ->where('allowances.id', $id)
            ->select('allowances.*', 'ei.name as employee_name', 'at.name as allowance_types_name')
            ->first();
        return $allowance;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->allowances as $data) {
                $allowance = Allowance::find($data['id']);

                if (!$allowance) {
                    continue; // Skip if not found
                }

                // Update state details
                $allowance->update([
                    'employee_id' => $data['employee_id'] ?? $allowance->employee_id,
                    'allowance_type_id' => $data['allowance_type_id'] ?? $allowance->allowance_type_id,
                    'description' => $data['description'] ?? $allowance->description,
                    'amount' => $data['amount'] ?? $allowance->amount,
                    'date' => $data['date'] ?? $allowance->date,
                    'posted' => $data['posted'] ?? $allowance->posted,
                ]);
                // Log activity for update
                ActivityLogger::log('Allowance Updated', 'Allowance', 'Allowance', $allowance->id, [
                    'employee_id' => $allowance->employee_id ?? '',
                    'amount' => $allowance->amount ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Allowance: ', [
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
