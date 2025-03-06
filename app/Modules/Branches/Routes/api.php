<?php

use App\Modules\Branches\Controllers\BranchController;
use Illuminate\Support\Facades\Route;


Route::prefix('branches')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [BranchController::class, 'index'])->name('branches.list'); // List data
    Route::post('/create', [BranchController::class, 'store'])->name('branches.store'); // Create data
    Route::post('/import', [BranchController::class, 'import'])->name('branches.import'); // import data
    Route::put('/bulk-update', [BranchController::class, 'bulkUpdate'])->name('branches.bulkUpdate'); // Bulk update
    Route::get('/view/{branch}', [BranchController::class, 'show'])->name('branches.view'); // View data
    Route::put('/update/{branch}', [BranchController::class, 'update'])->name('branches.update'); // Update data
});
