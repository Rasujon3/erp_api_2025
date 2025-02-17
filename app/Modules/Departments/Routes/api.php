<?php

use App\Modules\Departments\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/departments')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);          // List states
    Route::get('/summary', [DepartmentController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [DepartmentController::class, 'getDepartmentsDataTable']);  // Get DataTable data
    Route::get('/{department}', [DepartmentController::class, 'show']);    // View states
    Route::post('/', [DepartmentController::class, 'store']);           // Create states
    Route::put('/{department}', [DepartmentController::class, 'update']);  // Update states
    Route::delete('/{department}', [DepartmentController::class, 'destroy']); // Delete states
});
