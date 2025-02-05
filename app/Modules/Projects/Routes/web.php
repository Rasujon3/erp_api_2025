<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Projects\Controllers\ProjectController;



Route::prefix('/projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
});
