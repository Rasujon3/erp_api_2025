<?php

use App\Modules\SubCategory\Controllers\SubCategoryController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/sub-categories')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SubCategoryController::class, 'index']);          // List states
    Route::get('/summary', [SubCategoryController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [SubCategoryController::class, 'getSubCategoryDataTable']);  // Get DataTable data
    Route::get('/{subCategory}', [SubCategoryController::class, 'show']);    // View states
    Route::post('/', [SubCategoryController::class, 'store']);           // Create states
    Route::put('/{subCategory}', [SubCategoryController::class, 'update']);  // Update states
    Route::delete('/{subCategory}', [SubCategoryController::class, 'destroy']); // Delete states
});
