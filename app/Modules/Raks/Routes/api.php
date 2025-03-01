<?php

use App\Modules\Raks\Controllers\RakController;
use Illuminate\Support\Facades\Route;


Route::prefix('raks')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [RakController::class, 'index'])->name('raks.list'); // List data
    Route::post('/create', [RakController::class, 'store'])->name('raks.store'); // Create data
    Route::post('/import', [RakController::class, 'import'])->name('raks.import'); // import data
    Route::put('/bulk-update', [RakController::class, 'bulkUpdate'])->name('raks.bulkUpdate'); // Bulk update
    Route::get('/view/{rak}', [RakController::class, 'show'])->name('raks.view'); // View data
    Route::put('/update/{rak}', [RakController::class, 'update'])->name('raks.update'); // Update data
});
