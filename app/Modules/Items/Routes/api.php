<?php

use App\Modules\Items\Controllers\ItemController;
use App\Modules\Items\Controllers\ItemGroupController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/items')->group(function () {
    Route::get('/', [ItemController::class, 'index']);          // List states
    Route::get('/summary', [ItemController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [ItemController::class, 'getItemsDataTable']);  // Get DataTable data
    Route::get('/{item}', [ItemController::class, 'show']);    // View states
    Route::post('/', [ItemController::class, 'store']);           // Create states
    Route::put('/{item}', [ItemController::class, 'update']);  // Update states
    Route::delete('/{item}', [ItemController::class, 'destroy']); // Delete states
});
//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/item-groups')->group(function () {
    Route::get('/', [ItemGroupController::class, 'index']);          // List states
    Route::get('/summary', [ItemGroupController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [ItemGroupController::class, 'getItemGroupsDataTable']);  // Get DataTable data
    Route::get('/{itemGroup}', [ItemGroupController::class, 'show']);    // View states
    Route::post('/', [ItemGroupController::class, 'store']);           // Create states
    Route::put('/{itemGroup}', [ItemGroupController::class, 'update']);  // Update states
    Route::delete('/{itemGroup}', [ItemGroupController::class, 'destroy']); // Delete states
});
