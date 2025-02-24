<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Countries\Controllers\CountryController;


Route::prefix('countries')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CountryController::class, 'index']);          // List data
    Route::get('/datatable', [CountryController::class, 'getCountriesDataTable']);  // Get DataTable data
    Route::get('/summary', [CountryController::class, 'getSummary']); // Get summary data
    Route::get('/map', [CountryController::class, 'getMapData']);    // get map data
    Route::get('/generatePdf', [CountryController::class, 'generatePdf']);    // create pdf with all data
    Route::get('/generateSinglePdf/{country}', [CountryController::class, 'generateSinglePdf']);    // create pdf with specific data
    Route::get('/generateExcel', [CountryController::class, 'generateExcel']);    // create Excel with all data
    Route::get('/generateSingleExcel/{country}', [CountryController::class, 'generateSingleExcel']);    // create Excel with specific data
    Route::get('/view/{country}', [CountryController::class, 'show']);    // View data
    Route::post('/create', [CountryController::class, 'store']);           // Create data
    Route::put('/bulk-update', [CountryController::class, 'bulkUpdate']); // Bulk update
    Route::put('/update/{country}', [CountryController::class, 'update']);  // Update data
    Route::delete('/{country}', [CountryController::class, 'destroy']); // Delete data
});
