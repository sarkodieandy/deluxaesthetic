<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LinkedAccountController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:12,1')->group(function () {
    Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    Route::get('auth/google/select-account-type', [GoogleAuthController::class, 'selectAccountType'])->name('auth.google.select-account-type');
    Route::post('auth/google/select-account-type', [GoogleAuthController::class, 'storeAccountType'])->name('auth.google.select-account-type.store');
    Route::get('auth/google/complete-profile', [GoogleAuthController::class, 'completeProfile'])->name('auth.google.complete-profile');
    Route::post('auth/google/complete-profile', [GoogleAuthController::class, 'storeCompleteProfile'])->name('auth.google.complete-profile.store');
    Route::get('auth/google/link-account', [GoogleAuthController::class, 'linkAccountForm'])->name('auth.google.link-account');
    Route::post('auth/google/link-account', [GoogleAuthController::class, 'linkAccount'])->name('auth.google.link-account.store');
});

Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('account/linked-accounts', [LinkedAccountController::class, 'index'])->name('account.linked-accounts');
    Route::post('account/google/link', [LinkedAccountController::class, 'linkRedirect'])->name('account.google.link');
    Route::delete('account/google/unlink', [LinkedAccountController::class, 'unlink'])->name('account.google.unlink');
});
