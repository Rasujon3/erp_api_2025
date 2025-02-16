<?php

use App\Modules\Sizes\Controllers\SizeController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/sizes')->group(function () {
    Route::get('/', [SizeController::class, 'index']);          // List states
    Route::get('/summary', [SizeController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [SizeController::class, 'getSizesDataTable']);  // Get DataTable data
    Route::get('/{size}', [SizeController::class, 'show']);    // View states
    Route::post('/', [SizeController::class, 'store']);           // Create states
    Route::put('/{size}', [SizeController::class, 'update']);  // Update states
    Route::delete('/{size}', [SizeController::class, 'destroy']); // Delete states
});
