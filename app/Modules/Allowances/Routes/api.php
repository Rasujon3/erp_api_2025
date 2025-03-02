<?php

use App\Modules\Loans\Controllers\LoanController;
use Illuminate\Support\Facades\Route;


Route::prefix('allowances')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [LoanController::class, 'index'])->name('allowances.list'); // List data
    Route::post('/create', [LoanController::class, 'store'])->name('allowances.store'); // Create data
    Route::post('/import', [LoanController::class, 'import'])->name('allowances.import'); // import data
    Route::put('/bulk-update', [LoanController::class, 'bulkUpdate'])->name('allowances.bulkUpdate'); // Bulk update
    Route::get('/view/{bonus}', [LoanController::class, 'show'])->name('allowances.view'); // View data
    Route::put('/update/{bonus}', [LoanController::class, 'update'])->name('allowances.update'); // Update data
});
