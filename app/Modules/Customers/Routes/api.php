<?php

use App\Modules\Customers\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;


Route::prefix('customers')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CustomerController::class, 'index'])->name('customers.list'); // List data
    Route::post('/create', [CustomerController::class, 'store'])->name('customers.store'); // Create data
    Route::post('/import', [CustomerController::class, 'import'])->name('customers.import'); // import data
    Route::put('/bulk-update', [CustomerController::class, 'bulkUpdate'])->name('customers.bulkUpdate'); // Bulk update
    Route::get('/view/{customer}', [CustomerController::class, 'show'])->name('customers.view'); // View data
    Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('customers.update'); // Update data
});
