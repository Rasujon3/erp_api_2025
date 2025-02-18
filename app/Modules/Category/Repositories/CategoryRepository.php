<?php

namespace App\Modules\Category\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Category\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CategoryRepository
{
    public function getSummaryData()
    {
        $departments = Category::withTrashed()->get(); // Load all records including soft-deleted

        $totalDepartments = $departments->count();

        return [
            'totalDepartments' => $totalDepartments,
        ];
    }
    public function all()
    {
        return Category::cursor(); // Load all records
    }

    public function store(array $data): ?Category
    {
        try {
            DB::beginTransaction();

            // Create the Category record in the database
            $category = Category::create($data);

            // Log activity
            ActivityLogger::log('Category Add', 'Category', 'Category', $category->id, [
                'name' => $category->name ?? '',
                'description' => $category->description ?? ''
            ]);

            DB::commit();

            return $category;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Category: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Category $category, array $data): ?Category
    {
        try {
            DB::beginTransaction();

            // Perform the update
            $category->update($data);
            // Log activity for update
            ActivityLogger::log('Category Updated', 'Category', 'Category', $category->id, [
                'name' => $category->name
            ]);

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Category: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }


    /**
     * @throws Exception
     */
    public function delete(Category $category): bool
    {
        try {
            DB::beginTransaction();
            // Perform soft delete
            $deleted = $category->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            // Log activity after successful deletion
            ActivityLogger::log('Category Deleted', 'Category', 'Category', $category->id, [
                'name' => $category->name ?? '',
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Category: ' . $e->getMessage(), [
                'category_id' => $category->id,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }


    public function find($id)
    {
        return Category::find($id);
    }
    public function getData($id)
    {
        $category = Category::leftJoin('departments', 'categories.department_id', '=', 'departments.id')
            ->where('categories.id', $id)
            ->select(['categories.*', 'departments.name as department_name'])
            ->first();
        return $category;
    }
    public function checkExist($id)
    {
        $exist = Category::where('group_id', $id)->exists();
        if ($exist) {
            return true;
        }
        return false;
    }
}
