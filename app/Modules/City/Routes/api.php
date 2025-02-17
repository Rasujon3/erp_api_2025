<?php

use Illuminate\Support\Facades\Route;

use App\Modules\City\Controllers\CityController;


Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CityController::class, 'index']);          // List states
    Route::get('/datatable', [CityController::class, 'getCitiesDataTable']);  // Get DataTable data
    Route::get('/summary', [CityController::class, 'getSummary']); // Get states summary
    Route::get('/{city}', [CityController::class, 'show']);    // View states
    Route::post('/', [CityController::class, 'store']);           // Create states
    Route::put('/{city}', [CityController::class, 'update']);  // Update states
    Route::delete('/{city}', [CityController::class, 'destroy']); // Delete states
});
