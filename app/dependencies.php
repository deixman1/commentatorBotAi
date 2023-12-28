<?php

declare(strict_types=1);

use App\Infrastructure\OpenAi\OpenAiApiService;
use App\Infrastructure\RabbitMq\MessageBus;
use App\Infrastructure\Telegram\TelegramApiService;
use App\Infrastructure\Vk\VkApiService;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

return static function (ContainerBuilder $containerBuilder) {
    // Определение всех библиотечных зависимостей
    $containerBuilder->addDefinitions(
        [
            MessageBus::class => function (ContainerInterface $container) {
                return new MessageBus(
                    projectName: $container->get('settings')['rabbit']['project'],
                    uri: $container->get('settings')['rabbit']['uri']
                );
            },
            Client::class => function (ContainerInterface $container) {
                return new Client([
                    'timeout' => 300,
                ]);
            },
            OpenAiApiService::class => function (ContainerInterface $container) {
                return new OpenAiApiService(
                    $container->get(Client::class),
                    $container->get('settings')['openAi']['token'],
                );
            },
            TelegramApiService::class => function (ContainerInterface $container) {
                return new TelegramApiService(
                    $container->get(Client::class),
                    $container->get('settings')['telegramBot']['botToken']
                );
            },
            VkApiService::class => function (ContainerInterface $container) {
                return new VkApiService(
                    $container->get(LoggerInterface::class),
                    $container->get(Client::class),
                    $container->get('settings')['vkBot']['botToken'],
                    $container->get('settings')['vkBot']['version'],
                );
            },
            LoggerInterface::class => function (ContainerInterface $container) {
                $loggerSettings = $container->get('settings')['loggerSettings'];
                $logger = new MonologLogger('api');
                $logger->pushHandler(new StreamHandler($loggerSettings['path'] . '/api.log'));
                return $logger;
            },
            Environment::class => static function (ContainerInterface $container): Environment {
                $config = $container->get('settings')['twig'];

                $loader = new FilesystemLoader();

                foreach ($config['template_dirs'] as $alias => $dir) {
                    $loader->addPath($dir, $alias);
                }

                $environment = new Environment($loader, [
                    'cache' => $config['debug'] ? false : $config['cache_dir'],
                    'debug' => $config['debug'],
                    'strict_variables' => $config['debug'],
                    'auto_reload' => $config['debug'],
                ]);

                if ($config['debug']) {
                    $environment->addExtension(new DebugExtension());
                }

                foreach ($config['extensions'] as $class) {
                    /** @var ExtensionInterface $extension */
                    $extension = $container->get($class);
                    $environment->addExtension($extension);
                }

                $environment->addGlobal('asserts_path', $config['asserts_path']);

                return $environment;
            },
        ]
    );
};
