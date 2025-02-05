<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Admin\Controllers\CountryController;


Route::prefix('admin/countries')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CountryController::class, 'index']);          // List countries
    Route::post('/', [CountryController::class, 'store']);           // Create country
    Route::get('/datatable', [CountryController::class, 'getCountriesDataTable']);  // Get DataTable data
    Route::get('/summary', [CountryController::class, 'getSummary']); // Get country summary
    Route::get('/{country}', [CountryController::class, 'show']);    // View country
    Route::put('/{country}', [CountryController::class, 'update']);  // Update country
    Route::delete('/{country}', [CountryController::class, 'destroy']); // Delete country
});
