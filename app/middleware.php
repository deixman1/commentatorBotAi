<?php
declare(strict_types=1);

use App\Application\Middleware\DecodeRequestBodyMiddleware;
use Slim\App;

return static function (App $app) {
    $app->add(DecodeRequestBodyMiddleware::class);
};
