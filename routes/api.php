<?php

use App\Http\Controllers\Webhooks\ExpressPayWebhookController;
use Illuminate\Support\Facades\Route;

/*
| Webhooks and limited public API endpoints.
| expressPay notifications are verified against its authenticated Query API.
*/

Route::post('/webhooks/expresspay', ExpressPayWebhookController::class)
    ->middleware('throttle:120,1')->name('api.webhooks.expresspay');

Route::prefix('api')->group(function () {
    Route::get('/health', fn () => response()->json(['status' => 'ok']))->name('api.health');
});
