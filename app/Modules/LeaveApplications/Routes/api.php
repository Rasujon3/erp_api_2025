<?php

use App\Modules\LeaveApplications\Controllers\LeaveApplicationController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/leave/applications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [LeaveApplicationController::class, 'index']);          // List states
    Route::get('/summary', [LeaveApplicationController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [LeaveApplicationController::class, 'getLeaveApplicationsDataTable']);  // Get DataTable data
    Route::get('/{leaveApplication}', [LeaveApplicationController::class, 'show']);    // View states
    Route::post('/', [LeaveApplicationController::class, 'store']);           // Create states
    Route::put('/{leaveApplication}', [LeaveApplicationController::class, 'update']);  // Update states
    Route::delete('/{leaveApplication}', [LeaveApplicationController::class, 'destroy']); // Delete states
});
