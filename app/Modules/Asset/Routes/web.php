<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Asset\Controllers\AssetController;



Route::prefix('/asset')->group(function () {
    Route::get('/', [AssetController::class, 'index'])->name('asset.index');
});
