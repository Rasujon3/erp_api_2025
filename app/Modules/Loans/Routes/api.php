<?php

use App\Modules\Loans\Controllers\LoanController;
use Illuminate\Support\Facades\Route;


Route::prefix('loans')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [LoanController::class, 'index'])->name('loans.list'); // List data
    Route::post('/create', [LoanController::class, 'store'])->name('loans.store'); // Create data
    Route::post('/import', [LoanController::class, 'import'])->name('loans.import'); // import data
    Route::put('/bulk-update', [LoanController::class, 'bulkUpdate'])->name('loans.bulkUpdate'); // Bulk update
    Route::get('/view/{bonus}', [LoanController::class, 'show'])->name('loans.view'); // View data
    Route::put('/update/{bonus}', [LoanController::class, 'update'])->name('loans.update'); // Update data
});
