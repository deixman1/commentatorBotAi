<?php

namespace App\Shared\Logger;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public function createSingle(string $fileName): LoggerInterface
    {
        $channel = Log::build([
            'driver' => 'single',
            'path' => storage_path("logs/$fileName.log"),
        ]);
        return $channel;
    }
}
