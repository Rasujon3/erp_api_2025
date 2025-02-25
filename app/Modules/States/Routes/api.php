<?php

use Illuminate\Support\Facades\Route;

use App\Modules\States\Controllers\StateController;


Route::prefix('states')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [StateController::class, 'index']);          // List data
    Route::post('/create', [StateController::class, 'store']);           // Create data
    Route::put('/bulk-update', [StateController::class, 'bulkUpdate']); // Bulk update
    Route::get('/view/{state}', [StateController::class, 'show']);    // View data
    Route::put('/update/{state}', [StateController::class, 'update']);  // Update data
});
