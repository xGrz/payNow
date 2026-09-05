<?php

use Illuminate\Support\Facades\Route;
use Xgrz\PayNow\Http\Controllers\NotificationWebhookController;

if (config('paynow.endpoint.active', false)) {
    Route::middleware(['api'])
        ->name('paynow-notification-webhook')
        ->post(
            config('paynow.routing.notification_endpoint_url', 'paynow-webhook'),
            NotificationWebhookController::class
        );
}
