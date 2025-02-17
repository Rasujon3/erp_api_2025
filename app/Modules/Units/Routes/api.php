<?php

use App\Modules\Units\Controllers\UnitController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/units')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UnitController::class, 'index']);          // List states
    Route::get('/summary', [UnitController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [UnitController::class, 'getSizesDataTable']);  // Get DataTable data
    Route::get('/{unit}', [UnitController::class, 'show']);    // View states
    Route::post('/', [UnitController::class, 'store']);           // Create states
    Route::put('/{unit}', [UnitController::class, 'update']);  // Update states
    Route::delete('/{unit}', [UnitController::class, 'destroy']); // Delete states
});
