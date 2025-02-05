<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Hrm\Controllers\HrmController;



Route::prefix('/hrm')->group(function () {
    Route::get('/', [HrmController::class, 'index'])->name('hrm.index');
});
