<?php

namespace App\Modules\Loans\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Loans\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LoanRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $loans = Loan::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $loans->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $loans->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $loans->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $loans->whereNotNull('deleted_at')->count();
        $totalUpdated = $loans->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalLoans = $loans->whereNull('deleted_at')->count();

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
        $query = Loan::withTrashed()
            ->leftJoin('employees as ei', 'loan.employee_id', '=', 'ei.id')
            ->leftJoin('employees as py', 'loan.permitted_by', '=', 'py.id')
            ->select('loan.*', 'ei.name as employee_name', 'py.name as permitted_by_name');

        if ($request->has('draft')) {
            $query->where('loan.draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('loan.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('loan.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('loan.deleted_at');
            } else {
                $query->whereNull('loan.deleted_at');
            }
        } else {
            $query->whereNull('loan.deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('loan.updated_at');
            } else {
                $query->whereNull('loan.updated_at');
            }
        }
        if ($request->has('country_id')) {
            $query->where('loan.country_id', $request->input('country_id'));
        }
        if ($request->has('state_id')) {
            $query->where('loan.state_id', $request->input('state_id'));
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?Loan
    {
        DB::beginTransaction();
        try {
            /*
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }
            */

            // Create the Loan record in the database
            $loan = Loan::create($data);

            // Log activity
            ActivityLogger::log('Loan Add', 'Loan', 'Loan', $loan->id, [
                'employee_id' => $loan->employee_id ?? '',
                'amount' => $loan->amount ?? ''
            ]);

            DB::commit();

            return $loan;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Loan: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Loan $loan, array $data): ?Loan
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
            $loan->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($loan);
            } else {
                // Log activity for update
                ActivityLogger::log('Loan Updated', 'Loan', 'Loan', $loan->id, [
                    'employee_id' => $loan->employee_id ?? '',
                    'amount' => $loan->amount ?? ''
                ]);
            }

            DB::commit();
            return $loan;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Loan: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Loan $loan): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $loan->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Loan Deleted', 'Loan', 'Loan', $loan->id, [
                'employee_id' => $loan->employee_id ?? '',
                'amount' => $loan->amount ?? ''
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Loan: ' , [
                'state_id' => $loan->id,
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
        return Loan::find($id);
    }
    public function getData($id)
    {
        $loan = Loan::leftJoin('employees as ei', 'loan.employee_id', '=', 'ei.id')
            ->leftJoin('employees as py', 'loan.permitted_by', '=', 'py.id')
            ->where('loan.id', $id)
            ->select('loan.*', 'ei.name as employee_name', 'py.name as permitted_by_name')
            ->first();
        return $loan;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->loans as $data) {
                $loan = Loan::find($data['id']);

                if (!$loan) {
                    continue; // Skip if not found
                }

                // Update state details
                $loan->update([
                    'employee_id' => $data['employee_id'] ?? $loan->employee_id,
                    'permitted_by' => $data['permitted_by'] ?? $loan->permitted_by,
                    'description' => $data['description'] ?? $loan->description,
                    'amount' => $data['amount'] ?? $loan->amount,
                    'approved_date' => $data['approved_date'] ?? $loan->approved_date,
                    'repayment_from' => $data['repayment_from'] ?? $loan->repayment_from,
                    'interest_percentage' => $data['interest_percentage'] ?? $loan->interest_percentage,
                    'installment_period' => $data['installment_period'] ?? $loan->installment_period,
                    'repayment_amount' => $data['repayment_amount'] ?? $loan->repayment_amount,
                    'installment' => $data['installment'] ?? $loan->installment,
                    'status' => $data['status'] ?? $loan->status,
                    'date' => $data['date'] ?? $loan->date,
                    'posted' => $data['posted'] ?? $loan->posted,
                ]);
                // Log activity for update
                ActivityLogger::log('Loan Updated', 'Loan', 'Loan', $loan->id, [
                    'employee_id' => $loan->employee_id ?? '',
                    'amount' => $loan->amount ?? ''
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Loan: ', [
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
