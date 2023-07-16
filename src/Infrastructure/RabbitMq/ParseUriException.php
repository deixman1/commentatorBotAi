<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq;

use Exception;
use Throwable;

class ParseUriException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Ошибка парсина uri: ' . $message, $code, $previous);
    }
}
