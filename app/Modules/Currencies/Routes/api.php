<?php

use App\Modules\Currencies\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;


Route::prefix('currencies')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CurrencyController::class, 'index'])->name('currencies.list'); // List data
    Route::post('/create', [CurrencyController::class, 'store'])->name('currencies.store'); // Create data
    Route::post('/import', [CurrencyController::class, 'import'])->name('currencies.import'); // import data
    Route::put('/bulk-update', [CurrencyController::class, 'bulkUpdate'])->name('currencies.bulkUpdate'); // Bulk update
    Route::get('/view/{currency}', [CurrencyController::class, 'show'])->name('currencies.view'); // View data
    Route::put('/update/{currency}', [CurrencyController::class, 'update'])->name('currencies.update'); // Update data
});
