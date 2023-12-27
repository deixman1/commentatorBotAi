<?php
declare(strict_types=1);

use App\Application\Action\TelegramWebhookController;
use App\Application\Action\VkWebhookController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return static function (App $app) {
    $app->any('/', function (ServerRequestInterface $request, ResponseInterface $response) {
        $response->getBody()->write("/");
        return $response;
    });
    $app->post('/telegram-webhook', TelegramWebhookController::class);
    $app->post('/vk-webhook', VkWebhookController::class);
};
