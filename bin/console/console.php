<?php

declare(strict_types=1);

use DiagnosticsBackService\Admin\Console\FixDiagnosticsJson;
use DiagnosticsBackService\Admin\Console\FixOptions;
use DiagnosticsBackService\Admin\Console\FixOptionsIsMediaNeeded;
use DiagnosticsBackService\Admin\Console\ImportCrmVehicle;
use DiagnosticsBackService\Admin\Console\ImportEngineMalfunction;
use DiagnosticsBackService\Admin\Console\ImportHintFiles;
use DiagnosticsBackService\Admin\Console\ImportInfoBlocks;
use DiagnosticsBackService\Admin\Console\InserTestUsers;
use DiagnosticsBackService\Admin\Console\UserTokenCommand;
use DiagnosticsBackService\Application\Console\ClearCache;
use DiagnosticsBackService\Application\Console\DeleteDiagnostics;
use DiagnosticsBackService\Application\Console\DoneMediaProcessing;
use DiagnosticsBackService\Application\Console\ExportDiagnosticStructureCommand;
use DiagnosticsBackService\Application\Console\FillVehicleNames;
use DiagnosticsBackService\Application\Console\GeneratePdfHashCommand;
use DiagnosticsBackService\Application\Console\TokenGenerateExternalApi;
use Dotenv\Dotenv;
use Monolog\Handler\ErrorLogHandler;
use Slim\App;
use Symfony\Component\Console\Application;

error_reporting(E_ALL);

if (file_exists(__DIR__. '/../../logs/.service_unavailable.txt')) {
    die("[". date("c")."] Сайт на тех.обслуживании.");
}

require_once __DIR__ . '/../../vendor/autoload.php';

Dotenv::createUnsafeImmutable(__DIR__ . '/../../app')->load();
/** @var App $app */
$app = require_once __DIR__ . '/../../app/bootstrap.php';

$application = new Application();

$loggerHandler = new ErrorLogHandler();
$container = $app->getContainer();
$application->addCommands(
    [
        $container->get(ClearCache::class),
        $container->get(DeleteDiagnostics::class),
        $container->get(DoneMediaProcessing::class),
        $container->get(ExportDiagnosticStructureCommand::class),
        $container->get(FixDiagnosticsJson::class),
        $container->get(FixOptions::class),
        $container->get(FixOptionsIsMediaNeeded::class),
        $container->get(ImportEngineMalfunction::class),
        $container->get(ImportHintFiles::class),
        $container->get(ImportInfoBlocks::class),
        $container->get(InserTestUsers::class),
        $container->get(GeneratePdfHashCommand::class),
        $container->get(FillVehicleNames::class),
        $container->get(ImportCrmVehicle::class),
        $container->get(TokenGenerateExternalApi::class),
        $container->get(UserTokenCommand::class),
    ]
);
$application->run();
