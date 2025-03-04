<?php

use App\Modules\Admin\Controllers\AdminUserController;
use App\Modules\Admin\Controllers\AdminUsersLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    // Admin User
    Route::prefix('users')->group(function () {
        Route::post('/registration', [AdminUserController::class, 'store'])->name('admin_users.store'); // Create data
        Route::post('/verify-otp', [AdminUsersLogController::class, 'verifyOtp'])->name('admin_users.verifyOTP'); //
        Route::post('/logout', [AdminUsersLogController::class, 'logout'])->name('admin_users.logout'); //
    });
});
