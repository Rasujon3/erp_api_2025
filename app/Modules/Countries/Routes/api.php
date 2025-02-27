<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Countries\Controllers\CountryController;


Route::prefix('countries')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CountryController::class, 'index'])->name('countries.list');  // List data
    Route::get('/datatable', [CountryController::class, 'getCountriesDataTable'])->name('countries.datatable');  // Get DataTable data
    Route::get('/summary', [CountryController::class, 'getSummary'])->name('countries.summary'); // Get summary data
    Route::get('/map', [CountryController::class, 'getMapData'])->name('countries.map'); // get map data
    Route::get('/generatePdf', [CountryController::class, 'generatePdf'])->name('countries.generatePdf'); // create pdf with all data
    Route::get('/generateSinglePdf/{country}', [CountryController::class, 'generateSinglePdf'])->name('countries.generateSinglePdf'); // create pdf with specific data
    Route::get('/generateExcel', [CountryController::class, 'generateExcel'])->name('countries.generateExcel'); // create Excel with all data
    Route::get('/generateSingleExcel/{country}', [CountryController::class, 'generateSingleExcel'])->name('countries.generateSingleExcel'); // create Excel with specific data
    Route::get('/view/{country}', [CountryController::class, 'show'])->name('countries.view'); // View data
    Route::post('/create', [CountryController::class, 'store'])->name('countries.store'); // Create data
    Route::post('/import', [CountryController::class, 'import'])->name('countries.import'); // import data
    Route::put('/bulk-update', [CountryController::class, 'bulkUpdate'])->name('countries.bulkUpdate'); // Bulk update
    Route::put('/update/{country}', [CountryController::class, 'update'])->name('countries.update'); // Update data
    Route::delete('/{country}', [CountryController::class, 'destroy'])->name('countries.delete'); // Delete data
});
