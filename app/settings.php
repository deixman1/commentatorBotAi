<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Twig\Loader\FilesystemLoader;

return static function (ContainerBuilder $containerBuilder) {
    // Глобальные настройки
    $settings = [
        'settings' => [
            'displayErrorDetails' => (bool)($_ENV['SLIM_DISPLAY_ERROR_DETAILS'] ?? false),
            'php-di' => [
                'enableCompilation' => (bool)$_ENV['PHP_DI_ENABLE_COMPILATION'],
                'compilationDir' => __DIR__ . '/../' . $_ENV['PHP_DI_COMPILATION_DIR'],
            ],
            'twig' => [
                'debug' => $_ENV['TWIG_DEBUG'],
                'cache_dir' => __DIR__ . '/../' . $_ENV['TWIG_CACHE_DIR'],
                'asserts_path' => __DIR__ . '/../' . $_ENV['TWIG_ASSERTS_PATH'],
                'template_dirs' => [
                    FilesystemLoader::MAIN_NAMESPACE => __DIR__ . '/../' . $_ENV['TWIG_TEMPLATE_DIR']
                ],
                'extensions' => []
            ],
            'telegramBot' => [
                'botToken' => $_ENV['TELEGRAM_BOT_TOKEN'],
                'chatId' => $_ENV['TELEGRAM_BOT_CHAT_ID'],
            ],
            'vkBot' => [
                'version' => $_ENV['VK_VERSION'],
                'botToken' => $_ENV['VK_BOT_TOKEN'],
                'chatId' => $_ENV['VK_BOT_CHAT_ID'],
            ],
            'loggerSettings' => [
                'path' => __DIR__ . '/../logs',
                'botToken' => $_ENV['TELEGRAM_BOT_LOGGER_TOKEN'],
                'chatId' => $_ENV['TELEGRAM_BOT_LOGGER_CHAT_ID'],
            ],
        ],
    ];
    $containerBuilder->addDefinitions($settings);

    if ($settings['settings']['php-di']['enableCompilation']) {
        $containerBuilder->enableCompilation($settings['settings']['php-di']['compilationDir']);
    }
};
