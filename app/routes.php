<?php
declare(strict_types=1);

use App\Application\Action\TelegramWebhookController;
use App\Application\Action\VkWebhookController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app) {
    $app->post('/telegram-webhook', TelegramWebhookController::class);
    $app->post('/vk-webhook', VkWebhookController::class);
};
