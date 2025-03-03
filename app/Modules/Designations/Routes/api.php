<?php

use App\Modules\Designations\Controllers\DesignationController;
use Illuminate\Support\Facades\Route;


Route::prefix('designations')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [DesignationController::class, 'index'])->name('designations.list'); // List data
    Route::post('/create', [DesignationController::class, 'store'])->name('designations.store'); // Create data
    Route::post('/import', [DesignationController::class, 'import'])->name('designations.import'); // import data
    Route::put('/bulk-update', [DesignationController::class, 'bulkUpdate'])->name('designations.bulkUpdate'); // Bulk update
    Route::get('/view/{designation}', [DesignationController::class, 'show'])->name('designations.view'); // View data
    Route::put('/update/{designation}', [DesignationController::class, 'update'])->name('designations.update'); // Update data
});
