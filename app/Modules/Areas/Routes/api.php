<?php

use App\Modules\Areas\Controllers\AreaController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/areas')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AreaController::class, 'index']);          // List states
    Route::get('/summary', [AreaController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [AreaController::class, 'getAreasDataTable']);  // Get DataTable data
    Route::get('/{area}', [AreaController::class, 'show']);    // View states
    Route::post('/', [AreaController::class, 'store']);           // Create states
    Route::put('/{area}', [AreaController::class, 'update']);  // Update states
    Route::delete('/{area}', [AreaController::class, 'destroy']); // Delete states
});
