<?php

use App\Modules\Stores\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

use App\Modules\City\Controllers\CityController;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/stores')->group(function () {
    Route::get('/', [StoreController::class, 'index']);          // List states
    Route::get('/summary', [StoreController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [StoreController::class, 'getStoresDataTable']);  // Get DataTable data
    Route::get('/{store}', [StoreController::class, 'show']);    // View states
    Route::post('/', [StoreController::class, 'store']);           // Create states
    Route::put('/{store}', [StoreController::class, 'update']);  // Update states
    Route::delete('/{store}', [StoreController::class, 'destroy']); // Delete states
});
