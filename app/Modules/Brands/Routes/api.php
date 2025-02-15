<?php

use App\Modules\Brands\Controllers\BrandController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);          // List states
    Route::get('/summary', [BrandController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [BrandController::class, 'getBrandsDataTable']);  // Get DataTable data
    Route::get('/{brand}', [BrandController::class, 'show']);    // View states
    Route::post('/', [BrandController::class, 'store']);           // Create states
    Route::put('/{brand}', [BrandController::class, 'update']);  // Update states
    Route::delete('/{brand}', [BrandController::class, 'destroy']); // Delete states
});
