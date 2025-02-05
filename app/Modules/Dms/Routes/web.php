<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dms\Controllers\DmsController;



Route::prefix('/dms')->group(function () {
    Route::get('/', [DmsController::class, 'index'])->name('dms.index');
});
