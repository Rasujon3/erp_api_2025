<?php

use App\Modules\Areas\Controllers\AreaController;
use App\Modules\Currencies\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'index']);          // List states
    Route::get('/summary', [CurrencyController::class, 'getSummary']); // Get states summary
    Route::get('/datatable', [CurrencyController::class, 'getCurrenciesDataTable']);  // Get DataTable data
    Route::get('/{currency}', [CurrencyController::class, 'show']);    // View states
    Route::post('/', [CurrencyController::class, 'store']);           // Create states
    Route::put('/{currency}', [CurrencyController::class, 'update']);  // Update states
    Route::delete('/{currency}', [CurrencyController::class, 'destroy']); // Delete states
});
