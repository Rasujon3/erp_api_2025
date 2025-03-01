<?php

namespace App\Modules\Bins\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Bins\Models\Bin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BinRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $bins = Bin::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $bins->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $bins->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $bins->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $bins->whereNotNull('deleted_at')->count();
        $totalUpdated = $bins->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalBins = $bins->whereNull('deleted_at')->count();

        return [
            'totalBins' => $totalBins,
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
        $query = Bin::withTrashed();

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
    public function store(array $data): ?Bin
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Create the Bin record in the database
            $bin = Bin::create($data);

            // Log activity
            ActivityLogger::log('Bin Add', 'Bin', 'Bin', $bin->id, [
                'code' => $bin->code ?? '',
                'name' => $bin->name ?? '',
            ]);

            DB::commit();

            return $bin;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Bin: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Bin $bin, array $data): ?Bin
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if ($data['draft'] == 1) {
                $data['drafted_at'] = now();
            }

            // Perform the update
            $bin->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (!empty($data['is_delete']) && $data['is_delete'] == 1) {
                $this->delete($bin);
            } else {
                // Log activity for update
                ActivityLogger::log('Bin Updated', 'Bin', 'Bin', $bin->id, [
                    'code' => $bin->code ?? '',
                    'name' => $bin->name ?? '',
                ]);
            }

            DB::commit();
            return $bin;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Bin: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Bin $bin): bool
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $bin->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Bin Deleted', 'Bin', 'Bin', $bin->id, [
                'code' => $bin->code ?? '',
                'name' => $bin->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Bin: ' , [
                'state_id' => $bin->id,
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
        return Bin::find($id);
    }
    public function getData($id)
    {
        $bin = Bin::leftJoin('countries', 'country_id', '=', 'countries.id')
            ->leftJoin('states', 'states.id', '=', 'state_id')
            ->where('id', $id)
            ->select('*', 'countries.name as country_name', 'states.name as state_name')
            ->first();
        return $bin;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->bins as $data) {
                $bin = Bin::find($data['id']);

                if (!$bin) {
                    continue; // Skip if city not found
                }

                // Update state details
                $bin->update([
                    'code' => $data['code'] ?? $bin->code,
                    'name' => $data['name'] ?? $bin->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $bin->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $bin->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $bin->is_default,
                    'draft' => $data['draft'] ?? $bin->draft,
                    'drafted_at' => $data['draft'] == 1 ? now() : $bin->drafted_at,
                    'is_active' => $data['is_active'] ?? $bin->is_active
                ]);
                // Log activity for update
                ActivityLogger::log('Bin Updated', 'Bin', 'Bin', $bin->id, [
                    'code' => $bin->code ?? '',
                    'name' => $bin->name ?? '',
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Bin: ', [
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
