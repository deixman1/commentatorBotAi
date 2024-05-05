<?php

use App\BotAi\Application\Action\TelegramWebhookController;
use App\BotAi\Application\Action\VkWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => 'Hello! It\'s the service!');
Route::post('/telegram-webhook', TelegramWebhookController::class);
Route::any('/vk-webhook', VkWebhookController::class);
