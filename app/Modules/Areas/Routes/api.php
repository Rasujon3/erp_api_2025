<?php

use App\Modules\Areas\Controllers\AreaController;
use Illuminate\Support\Facades\Route;


Route::prefix('areas')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [AreaController::class, 'index'])->name('areas.list'); // List data
    Route::post('/create', [AreaController::class, 'store'])->name('areas.store'); // Create data
    Route::post('/import', [AreaController::class, 'import'])->name('areas.import'); // import data
    Route::put('/bulk-update', [AreaController::class, 'bulkUpdate'])->name('areas.bulkUpdate'); // Bulk update
    Route::get('/view/{area}', [AreaController::class, 'show'])->name('areas.view'); // View data
    Route::put('/update/{area}', [AreaController::class, 'update'])->name('areas.update'); // Update data
});
