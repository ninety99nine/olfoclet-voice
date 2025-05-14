<?php

foreach (glob(__DIR__ . '/api/*.php') as $routeFile) {
    require $routeFile;
}

Route::get('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle'])->name('webhooks.whatsapp');
Route::post('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle'])->name('webhooks.whatsapp');
