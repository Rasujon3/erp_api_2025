<?php

use App\Modules\Leaves\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/leaves')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [LeaveController::class, 'index']);          // List states
    Route::get('/summary', [LeaveController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [LeaveController::class, 'getDepartmentsDataTable']);  // Get DataTable data
    Route::get('/{leave}', [LeaveController::class, 'show']);    // View states
    Route::post('/', [LeaveController::class, 'store']);           // Create states
    Route::put('/{leave}', [LeaveController::class, 'update']);  // Update states
    Route::delete('/{leave}', [LeaveController::class, 'destroy']); // Delete states
});
