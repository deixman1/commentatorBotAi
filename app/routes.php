<?php
declare(strict_types=1);

use App\Application\Action\TelegramWebhookController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app) {
    $app->post('/telegram-webhook', TelegramWebhookController::class);
};
