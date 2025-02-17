<?php

use App\Modules\TaxRates\Controllers\TaxRateController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/taxRates')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TaxRateController::class, 'index']);          // List states
    Route::get('/summary', [TaxRateController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [TaxRateController::class, 'getTaxRatesDataTable']);  // Get DataTable data
    Route::get('/{taxRate}', [TaxRateController::class, 'show']);    // View states
    Route::post('/', [TaxRateController::class, 'store']);           // Create states
    Route::put('/{taxRate}', [TaxRateController::class, 'update']);  // Update states
    Route::delete('/{taxRate}', [TaxRateController::class, 'destroy']); // Delete states
});
