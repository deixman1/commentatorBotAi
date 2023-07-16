<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $containerBuilder) {
    // Определение всех библиотечных зависимостей
    $containerBuilder->addDefinitions(
        [
            Client::class => function (ContainerInterface $container) {
                return new Client([
                    'timeout' => 30,
                ]);
            },
            LoggerInterface::class => function (ContainerInterface $container) {
                $loggerSettings = $container->get('settings')['loggerSettings'];
                $logger = new MonologLogger('api');
                $logger->pushHandler(new StreamHandler($loggerSettings['path'] . '/api.log'));
                return $logger;
            }
        ]
    );
};
