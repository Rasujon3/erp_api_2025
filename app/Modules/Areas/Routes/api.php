<?php

use App\Modules\Areas\Controllers\AreaController;
use Illuminate\Support\Facades\Route;


Route::prefix('areas')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [AreaController::class, 'index']);          // List data
    Route::post('/create', [AreaController::class, 'store']);           // Create data
    Route::put('/bulk-update', [AreaController::class, 'bulkUpdate']); // Bulk update
    Route::get('/view/{area}', [AreaController::class, 'show']);    // View data
    Route::put('/update/{area}', [AreaController::class, 'update']);  // Update data
});
