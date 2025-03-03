<?php

use App\Modules\Allowances\Controllers\AllowanceController;
use Illuminate\Support\Facades\Route;


Route::prefix('allowances')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [AllowanceController::class, 'index'])->name('allowances.list'); // List data
    Route::post('/create', [AllowanceController::class, 'store'])->name('allowances.store'); // Create data
    Route::post('/import', [AllowanceController::class, 'import'])->name('allowances.import'); // import data
    Route::put('/bulk-update', [AllowanceController::class, 'bulkUpdate'])->name('allowances.bulkUpdate'); // Bulk update
    Route::get('/view/{bonus}', [AllowanceController::class, 'show'])->name('allowances.view'); // View data
    Route::put('/update/{bonus}', [AllowanceController::class, 'update'])->name('allowances.update'); // Update data
});
