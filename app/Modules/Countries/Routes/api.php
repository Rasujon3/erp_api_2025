<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Countries\Controllers\CountryController;


Route::prefix('countries')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CountryController::class, 'index']);          // List data
    Route::get('/datatable', [CountryController::class, 'getCountriesDataTable']);  // Get DataTable data
    Route::get('/summary', [CountryController::class, 'getSummary']); // Get summary data
    Route::get('/{country}', [CountryController::class, 'show']);    // View data
    Route::post('/', [CountryController::class, 'store']);           // Create data
    Route::put('/{country}', [CountryController::class, 'update']);  // Update data
    Route::delete('/{country}', [CountryController::class, 'destroy']); // Delete data
});
