<?php

use App\Modules\AdminGroups\Controllers\AdminGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin-groups')->group(function () {
    Route::get('/list', [AdminGroupController::class, 'index'])->name('admin_groups.list'); // List data
    Route::post('/create', [AdminGroupController::class, 'store'])->name('admin_groups.store'); // Create data
    Route::post('/import', [AdminGroupController::class, 'import'])->name('admin_groups.import'); // import data
    Route::put('/bulk-update', [AdminGroupController::class, 'bulkUpdate'])->name('admin_groups.bulkUpdate'); // Bulk update
    Route::get('/view/{adminGroup}', [AdminGroupController::class, 'show'])->name('admin_groups.view'); // View data
    Route::put('/update/{adminGroup}', [AdminGroupController::class, 'update'])->name('admin_groups.update'); // Update data
});
