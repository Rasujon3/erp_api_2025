<?php

use App\Modules\Divisions\Controllers\DivisionController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/divisions')->group(function () {
    Route::get('/', [DivisionController::class, 'index']);          // List states
    Route::get('/summary', [DivisionController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [DivisionController::class, 'getTagsDataTable']);  // Get DataTable data
    Route::get('/{division}', [DivisionController::class, 'show']);    // View states
    Route::post('/', [DivisionController::class, 'store']);           // Create states
    Route::put('/{division}', [DivisionController::class, 'update']);  // Update states
    Route::delete('/{division}', [DivisionController::class, 'destroy']); // Delete states
});
