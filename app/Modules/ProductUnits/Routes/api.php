<?php

use App\Modules\ProductUnits\Controllers\ProductUnitController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/product-units')->group(function () {
    Route::get('/', [ProductUnitController::class, 'index']);          // List states
    Route::get('/summary', [ProductUnitController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [ProductUnitController::class, 'getProductUnitsDataTable']);  // Get DataTable data
    Route::get('/{productUnit}', [ProductUnitController::class, 'show']);    // View states
    Route::post('/', [ProductUnitController::class, 'store']);           // Create states
    Route::put('/{productUnit}', [ProductUnitController::class, 'update']);  // Update states
    Route::delete('/{productUnit}', [ProductUnitController::class, 'destroy']); // Delete states
});
