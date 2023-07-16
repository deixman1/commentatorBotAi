<?php

declare(strict_types=1);

use App\Application\Console\VkConsumer;
use Dotenv\Dotenv;
use Monolog\Handler\ErrorLogHandler;
use Slim\App;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../../vendor/autoload.php';

Dotenv::createUnsafeImmutable(__DIR__ . '/../../docker')->load();
/** @var App $app */
$app = require_once __DIR__ . '/../../app/bootstrap.php';

$application = new Application();

$loggerHandler = new ErrorLogHandler();
$container = $app->getContainer();
$application->addCommands(
    [
        $container->get(VkConsumer::class),
    ]
);
$application->run();
