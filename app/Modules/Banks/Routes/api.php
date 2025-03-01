<?php

use App\Modules\Banks\Controllers\BankController;
use Illuminate\Support\Facades\Route;


Route::prefix('banks')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [BankController::class, 'index'])->name('banks.list'); // List data
    Route::post('/create', [BankController::class, 'store'])->name('banks.store'); // Create data
    Route::post('/import', [BankController::class, 'import'])->name('banks.import'); // import data
    Route::put('/bulk-update', [BankController::class, 'bulkUpdate'])->name('banks.bulkUpdate'); // Bulk update
    Route::get('/view/{bank}', [BankController::class, 'show'])->name('banks.view'); // View data
    Route::put('/update/{bank}', [BankController::class, 'update'])->name('banks.update'); // Update data
});
