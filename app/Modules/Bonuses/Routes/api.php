<?php

use App\Modules\Bonuses\Controllers\BonusController;
use Illuminate\Support\Facades\Route;


Route::prefix('bonuses')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [BonusController::class, 'index'])->name('bonuses.list'); // List data
    Route::post('/create', [BonusController::class, 'store'])->name('bonuses.store'); // Create data
    Route::post('/import', [BonusController::class, 'import'])->name('bonuses.import'); // import data
    Route::put('/bulk-update', [BonusController::class, 'bulkUpdate'])->name('bonuses.bulkUpdate'); // Bulk update
    Route::get('/view/{bonus}', [BonusController::class, 'show'])->name('bonuses.view'); // View data
    Route::put('/update/{bonus}', [BonusController::class, 'update'])->name('bonuses.update'); // Update data
});
