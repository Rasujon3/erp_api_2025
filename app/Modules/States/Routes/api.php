<?php

use Illuminate\Support\Facades\Route;

use App\Modules\States\Controllers\StateController;


Route::prefix('states')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [StateController::class, 'index'])->name('states.list'); // List data
    Route::post('/create', [StateController::class, 'store'])->name('states.store'); // Create data
    Route::post('/import', [StateController::class, 'import'])->name('states.import'); // import data
    Route::put('/bulk-update', [StateController::class, 'bulkUpdate'])->name('states.bulkUpdate'); // Bulk update
    Route::get('/view/{state}', [StateController::class, 'show'])->name('states.view'); // View data
    Route::put('/update/{state}', [StateController::class, 'update'])->name('states.update'); // Update data
});
