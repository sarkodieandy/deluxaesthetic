<?php

use App\Http\Controllers\Trainer\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active.account', 'role:Trainer'])
    ->prefix('trainer')
    ->name('trainer.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
