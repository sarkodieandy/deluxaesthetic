<?php

use Illuminate\Support\Facades\Route;

/*
| Webhooks and limited public API endpoints.
| Payment / WhatsApp webhooks are registered in later phases with signature verification.
*/

Route::prefix('api')->group(function () {
    Route::get('/health', fn () => response()->json(['status' => 'ok']))->name('api.health');
});
