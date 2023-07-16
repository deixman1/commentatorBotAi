<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

setlocale(LC_ALL, "ru_RU.UTF-8");
date_default_timezone_set('Europe/Moscow');

$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

$env = $container->get('settings');

AppFactory::setContainer($container);

return AppFactory::create();
