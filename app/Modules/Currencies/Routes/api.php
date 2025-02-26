<?php

use App\Modules\Currencies\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;


Route::prefix('currencies')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CurrencyController::class, 'index']);          // List data
    Route::post('/create', [CurrencyController::class, 'store']);           // Create data
    Route::put('/bulk-update', [CurrencyController::class, 'bulkUpdate']); // Bulk update
    Route::get('/view/{currency}', [CurrencyController::class, 'show']);    // View data
    Route::put('/update/{currency}', [CurrencyController::class, 'update']);  // Update data
});
