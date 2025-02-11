<?php

use App\Modules\Areas\Controllers\AreaController;
use App\Modules\Branches\Controllers\BranchController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/branches')->group(function () {
    Route::get('/', [BranchController::class, 'index']);          // List states
    Route::get('/summary', [BranchController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [BranchController::class, 'getAreasDataTable']);  // Get DataTable data
    Route::get('/{branch}', [BranchController::class, 'show']);    // View states
    Route::post('/', [BranchController::class, 'store']);           // Create states
    Route::put('/{branch}', [BranchController::class, 'update']);  // Update states
    Route::delete('/{branch}', [BranchController::class, 'destroy']); // Delete states
});
