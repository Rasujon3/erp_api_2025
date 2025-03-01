<?php

namespace App\Modules\Raks\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Raks\Models\Rak;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RakRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $raks = Rak::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $raks->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $raks->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $raks->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $raks->whereNotNull('deleted_at')->count();
        $totalUpdated = $raks->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalRaks = $raks->whereNull('deleted_at')->count();

        return [
            'totalRaks' => $totalRaks,
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
        $query = Rak::withTrashed();

        if ($request->has('draft')) {
            $query->where('draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('deleted_at');
            } else {
                $query->whereNull('deleted_at');
            }
        } else {
            $query->whereNull('deleted_at');
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('updated_at');
            } else {
                $query->whereNull('updated_at');
            }
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?Rak
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Create the Rak record in the database
            $rak = Rak::create($data);

            // Log activity
            ActivityLogger::log('Rak Add', 'Rak', 'Rak', $rak->id, [
                'code' => $rak->code ?? '',
                'name' => $rak->name ?? '',
            ]);

            DB::commit();

            return $rak;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Rak: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Rak $rak, array $data): ?Rak
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Perform the update
            $rak->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($rak);
            } else {
                // Log activity for update
                ActivityLogger::log('Rak Updated', 'Rak', 'Rak', $rak->id, [
                    'code' => $rak->code ?? '',
                    'name' => $rak->name ?? '',
                ]);
            }

            DB::commit();
            return $rak;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Rak: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Rak $rak): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $rak->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Rak Deleted', 'Rak', 'Rak', $rak->id, [
                'code' => $rak->code ?? '',
                'name' => $rak->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Rak: ' , [
                'state_id' => $rak->id,
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
        return Rak::find($id);
    }
    public function getData($id)
    {
        $rak = Rak::leftJoin('countries', 'country_id', '=', 'countries.id')
            ->leftJoin('states', 'states.id', '=', 'state_id')
            ->where('id', $id)
            ->select('*', 'countries.name as country_name', 'states.name as state_name')
            ->first();
        return $rak;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->raks as $data) {
                $rak = Rak::find($data['id']);

                if (!$rak) {
                    continue; // Skip if city not found
                }

                // Update state details
                $rak->update([
                    'code' => $data['code'] ?? $rak->code,
                    'name' => $data['name'] ?? $rak->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $rak->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $rak->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $rak->is_default,
                    'draft' => $data['draft'] ?? $rak->draft,
                    'drafted_at' => $data['draft'] == 1 ? now() : $rak->drafted_at,
                    'is_active' => $data['is_active'] ?? $rak->is_active
                ]);
                // Log activity for update
                ActivityLogger::log('Rak Updated', 'Rak', 'Rak', $rak->id, [
                    'code' => $rak->code ?? '',
                    'name' => $rak->name ?? '',
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Rak: ', [
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
