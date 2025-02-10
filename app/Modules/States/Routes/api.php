<?php

use Illuminate\Support\Facades\Route;

use App\Modules\States\Controllers\StateController;


Route::prefix('admin/states')->middleware('auth:sanctum')->group(function () {
//Route::prefix('admin/states')->group(function () {
    Route::get('/', [StateController::class, 'index']);          // List states
    Route::post('/', [StateController::class, 'store']);           // Create states
    Route::get('/datatable', [StateController::class, 'getStatesDataTable']);  // Get DataTable data
    Route::get('/summary', [StateController::class, 'getSummary']); // Get states summary
    Route::get('/{state}', [StateController::class, 'show']);    // View states
    Route::put('/{state}', [StateController::class, 'update']);  // Update states
    Route::delete('/{state}', [StateController::class, 'destroy']); // Delete states
});
