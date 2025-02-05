<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Crm\Controllers\CrmController;



Route::prefix('/crm')->group(function () {
    Route::get('/', [CrmController::class, 'index'])->name('crm.index');
});
