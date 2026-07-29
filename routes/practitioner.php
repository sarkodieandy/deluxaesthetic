<?php

use App\Http\Controllers\Practitioner\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active.account', 'role:Practitioner|Therapist'])
    ->prefix('practitioner')
    ->name('practitioner.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
