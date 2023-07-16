<?php
declare(strict_types=1);

namespace App\Application\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractController
{
    protected ServerRequestInterface $request;
    protected ResponseInterface $response;
    protected array $args;

    public function __construct(
        protected readonly LoggerInterface $logger,
    )
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        return $this->execute();
    }

    protected abstract function execute(): ResponseInterface;
}