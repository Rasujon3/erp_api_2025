<?php

use Illuminate\Support\Facades\Route;

use App\Modules\City\Controllers\CityController;


Route::prefix('cities')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CityController::class, 'index']);          // List data
    Route::post('/create', [CityController::class, 'store']);           // Create data
    Route::put('/bulk-update', [CityController::class, 'bulkUpdate']); // Bulk update
    Route::get('/view/{city}', [CityController::class, 'show']);    // View data
    Route::put('/update/{city}', [CityController::class, 'update']);  // Update data
});
