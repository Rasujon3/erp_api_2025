<?php

use App\Modules\Areas\Controllers\AreaController;
use App\Modules\Branches\Controllers\BranchController;
use App\Modules\Settings\Controllers\SettingController;
use Illuminate\Support\Facades\Route;


//Route::prefix('admin/cities')->middleware('auth:sanctum')->group(function () {
Route::prefix('admin/settings')->group(function () {
    Route::get('/', [SettingController::class, 'index']);          // List states
    Route::post('/', [SettingController::class, 'update']);  // Update states
});
