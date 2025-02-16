<?php

use App\Modules\Colors\Controllers\ColorController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/colors')->group(function () {
    Route::get('/', [ColorController::class, 'index']);          // List states
    Route::get('/summary', [ColorController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [ColorController::class, 'getBrandsDataTable']);  // Get DataTable data
    Route::get('/{color}', [ColorController::class, 'show']);    // View states
    Route::post('/', [ColorController::class, 'store']);           // Create states
    Route::put('/{color}', [ColorController::class, 'update']);  // Update states
    Route::delete('/{color}', [ColorController::class, 'destroy']); // Delete states
});
