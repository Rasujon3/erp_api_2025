<?php

use App\Modules\Tags\Controllers\TagController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/tags')->group(function () {
    Route::get('/', [TagController::class, 'index']);          // List states
    Route::get('/summary', [TagController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [TagController::class, 'getTagsDataTable']);  // Get DataTable data
    Route::get('/{tag}', [TagController::class, 'show']);    // View states
    Route::post('/', [TagController::class, 'store']);           // Create states
    Route::put('/{tag}', [TagController::class, 'update']);  // Update states
    Route::delete('/{tag}', [TagController::class, 'destroy']); // Delete states
});
