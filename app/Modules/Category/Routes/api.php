<?php

use App\Modules\Category\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/categories')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);          // List states
    Route::get('/summary', [CategoryController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [CategoryController::class, 'getCategoryDataTable']);  // Get DataTable data
    Route::get('/{category}', [CategoryController::class, 'show']);    // View states
    Route::post('/', [CategoryController::class, 'store']);           // Create states
    Route::put('/{category}', [CategoryController::class, 'update']);  // Update states
    Route::delete('/{category}', [CategoryController::class, 'destroy']); // Delete states
});
