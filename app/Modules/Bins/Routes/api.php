<?php

use App\Modules\Bins\Controllers\BinController;
use Illuminate\Support\Facades\Route;


Route::prefix('bins')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [BinController::class, 'index'])->name('bins.list'); // List data
    Route::post('/create', [BinController::class, 'store'])->name('bins.store'); // Create data
    Route::post('/import', [BinController::class, 'import'])->name('bins.import'); // import data
    Route::put('/bulk-update', [BinController::class, 'bulkUpdate'])->name('bins.bulkUpdate'); // Bulk update
    Route::get('/view/{bin}', [BinController::class, 'show'])->name('bins.view'); // View data
    Route::put('/update/{bin}', [BinController::class, 'update'])->name('bins.update'); // Update data
});
