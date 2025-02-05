<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Tasks\Controllers\TaskController;



Route::prefix('/tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
});
