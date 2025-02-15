<?php

use App\Modules\Groups\Controllers\GroupController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/groups')->group(function () {
    Route::get('/', [GroupController::class, 'index']);          // List states
    Route::get('/summary', [GroupController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [GroupController::class, 'getTagsDataTable']);  // Get DataTable data
    Route::get('/{group}', [GroupController::class, 'show']);    // View states
    Route::post('/', [GroupController::class, 'store']);           // Create states
    Route::put('/{group}', [GroupController::class, 'update']);  // Update states
    Route::delete('/{group}', [GroupController::class, 'destroy']); // Delete states
});
