<?php

use Illuminate\Support\Facades\Route;

use App\Modules\City\Controllers\CityController;


Route::prefix('cities')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CityController::class, 'index'])->name('cities.list'); // List data
    Route::post('/create', [CityController::class, 'store'])->name('cities.store'); // Create data
    Route::post('/import', [CityController::class, 'import'])->name('cities.import'); // import data
    Route::put('/bulk-update', [CityController::class, 'bulkUpdate'])->name('cities.bulkUpdate'); // Bulk update
    Route::get('/view/{city}', [CityController::class, 'show'])->name('cities.view'); // View data
    Route::put('/update/{city}', [CityController::class, 'update'])->name('cities.update'); // Update data
});
