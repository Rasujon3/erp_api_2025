<?php

use App\Modules\Warehouses\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/warehouses')->group(function () {
    Route::get('/', [WarehouseController::class, 'index']);          // List states
    Route::get('/summary', [WarehouseController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [WarehouseController::class, 'getWarehouseDataTable']);  // Get DataTable data
    Route::get('/{warehouse}', [WarehouseController::class, 'show']);    // View states
    Route::post('/', [WarehouseController::class, 'store']);           // Create states
    Route::put('/{warehouse}', [WarehouseController::class, 'update']);  // Update states
    Route::delete('/{warehouse}', [WarehouseController::class, 'destroy']); // Delete states
});
