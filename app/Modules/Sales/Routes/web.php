<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sales\Controllers\SalesController;



Route::prefix('/sales')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('sales.index');
});
