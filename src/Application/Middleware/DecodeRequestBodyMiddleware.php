<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DecodeRequestBodyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $contents = $request->getBody()->getContents();

            $body = $contents ? json_decode($contents, true, 512, JSON_THROW_ON_ERROR) : [];

            $request = $request->withParsedBody($body);
        }

        return $handler->handle($request);
    }
}
