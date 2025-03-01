<?php

use App\Modules\Suppliers\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;


Route::prefix('suppliers')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [SupplierController::class, 'index'])->name('suppliers.list'); // List data
    Route::post('/create', [SupplierController::class, 'store'])->name('suppliers.store'); // Create data
    Route::post('/import', [SupplierController::class, 'import'])->name('suppliers.import'); // import data
    Route::put('/bulk-update', [SupplierController::class, 'bulkUpdate'])->name('suppliers.bulkUpdate'); // Bulk update
    Route::get('/view/{supplier}', [SupplierController::class, 'show'])->name('suppliers.view'); // View data
    Route::put('/update/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update'); // Update data
});
