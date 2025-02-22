<?php

use App\Modules\Leaves\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/leaves')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [LeaveController::class, 'index']);          // List data
    Route::get('/summary', [LeaveController::class, 'getSummary']); // Get summary data
    Route::get('/datatable', [LeaveController::class, 'getDepartmentsDataTable']);  // Get DataTable data
    Route::get('/{leave}', [LeaveController::class, 'show']);    // View data
    Route::post('/', [LeaveController::class, 'store']);           // Create data
    Route::put('/{leave}', [LeaveController::class, 'update']);  // Update data
    Route::delete('/{leave}', [LeaveController::class, 'destroy']); // Delete data
});
