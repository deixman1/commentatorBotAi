<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Handlers\ErrorHandler;

require_once __DIR__ . '/../vendor/autoload.php';

Dotenv::createUnsafeImmutable(__DIR__ . '/../docker')->load();

/** @var App $app */
$app = require_once __DIR__ . '/../app/bootstrap.php';

// Register middleware
$middleware = require_once __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require_once __DIR__ . '/../app/routes.php';
$routes($app);

/** @var bool $displayErrorDetails */
$displayErrorDetails = $app->getContainer()->get('settings')['displayErrorDetails'];

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Add Routing Middleware
$app->addRoutingMiddleware();

// Create Error Handler
$responseFactory = $app->getResponseFactory();

$loggerSettings = $app->getContainer()->get('settings')['loggerSettings'];
$fatalLogger = new MonologLogger('GLOBAL', [new TelegramBotHandler($loggerSettings['botToken'], $loggerSettings['chatId'])]);
$fatalLogger->pushHandler(new StreamHandler($loggerSettings['path'] . '/error.log', Level::Error));
$errorHandler = new ErrorHandler($app->getCallableResolver(), $responseFactory, $fatalLogger);

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

$errorMiddleware->setDefaultErrorHandler($errorHandler);

$app->run();

