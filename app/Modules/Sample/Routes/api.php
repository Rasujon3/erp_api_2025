<?php

use App\Modules\Items\Controllers\ItemGroupController;
use App\Modules\Sample\Controllers\SampleCategoryController;
use App\Modules\Sample\Controllers\SampleReceiveController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/sample-categories')->group(function () {
    Route::get('/', [SampleCategoryController::class, 'index']);          // List states
    Route::get('/summary', [SampleCategoryController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [SampleCategoryController::class, 'getSampleCategoriesDataTable']);  // Get DataTable data
    Route::get('/{category}', [SampleCategoryController::class, 'show']);    // View states
    Route::post('/', [SampleCategoryController::class, 'store']);           // Create states
    Route::put('/{category}', [SampleCategoryController::class, 'update']);  // Update states
    Route::delete('/{category}', [SampleCategoryController::class, 'destroy']); // Delete states
});
//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/sample-receiving')->group(function () {
    Route::get('/', [SampleReceiveController::class, 'index']);          // List states
    Route::get('/summary', [SampleReceiveController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [SampleReceiveController::class, 'getSampleReceivesDataTable']);  // Get DataTable data
    Route::get('/{receive}', [SampleReceiveController::class, 'show']);    // View states
    Route::post('/', [SampleReceiveController::class, 'store']);           // Create states
    Route::put('/{receive}', [SampleReceiveController::class, 'update']);  // Update states
    Route::delete('/{receive}', [SampleReceiveController::class, 'destroy']); // Delete states
});
