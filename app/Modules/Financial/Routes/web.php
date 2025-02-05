<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Financial\Controllers\FinancialController;



Route::prefix('/financial')->group(function () {
    Route::get('/', [FinancialController::class, 'index'])->name('financial.index');
});
