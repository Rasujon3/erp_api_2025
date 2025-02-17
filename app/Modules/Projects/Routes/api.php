<?php

use App\Modules\Projects\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/projects')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);          // List states
    Route::get('/summary', [ProjectController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [ProjectController::class, 'getSizesDataTable']);  // Get DataTable data
    Route::get('/{project}', [ProjectController::class, 'show']);    // View states
    Route::post('/', [ProjectController::class, 'store']);           // Create states
    Route::put('/{project}', [ProjectController::class, 'update']);  // Update states
    Route::delete('/{project}', [ProjectController::class, 'destroy']); // Delete states
});
