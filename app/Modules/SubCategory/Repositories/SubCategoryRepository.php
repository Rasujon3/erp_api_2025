<?php

namespace App\Modules\SubCategory\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\SubCategory\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SubCategoryRepository
{
    public function getSummaryData()
    {
        $subCategories = SubCategory::withTrashed()->get(); // Load all records including soft-deleted

        $totalSubCategories = $subCategories->count();

        return [
            'totalSubCategories' => $totalSubCategories,
        ];
    }
    public function all()
    {
        return SubCategory::cursor(); // Load all records
    }

    public function store(array $data): ?SubCategory
    {
        try {
            DB::beginTransaction();

            // Create the SubCategory record in the database
            $subCategory = SubCategory::create($data);

            // Log activity
            ActivityLogger::log('SubCategory Add', 'SubCategory', 'SubCategory', $subCategory->id, [
                'name' => $subCategory->name ?? '',
                'description' => $subCategory->description ?? ''
            ]);

            DB::commit();

            return $subCategory;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing SubCategory: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(SubCategory $subCategory, array $data): ?SubCategory
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $subCategory->update($data);
            // Log activity for update
            ActivityLogger::log('SubCategory Updated', 'SubCategory', 'SubCategory', $subCategory->id, [
                'name' => $subCategory->name
            ]);

            DB::commit();
            return $subCategory;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating SubCategory: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(SubCategory $subCategory): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $subCategory->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('SubCategory Deleted', 'SubCategory', 'SubCategory', $subCategory->id, [
                'name' => $subCategory->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting SubCategory: ' . $e->getMessage(), [
                'category_id' => $subCategory->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return SubCategory::find($id);
    }
    public function getData($id)
    {
        $subCategory = SubCategory::leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->where('categories.id', $id)
            ->select(['sub_categories.*', 'categories.name as category_name'])
            ->first();
        return $subCategory;
    }
    public function checkExist($id)
    {
        $exist = SubCategory::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
