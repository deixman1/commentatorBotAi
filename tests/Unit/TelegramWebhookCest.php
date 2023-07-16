<?php


namespace AppTest\Unit;

use App\Application\Action\TelegramWebhookController;
use AppTest\Support\UnitTester;
use Codeception\Stub;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;

class TelegramWebhookCest
{
    private Request $request;
    private Response $response;
    private array $args = [];

    public function _before(UnitTester $I): void
    {
        $this->request = new Request(
            method: 'POST',
            uri: new Uri(
                scheme: 'https',
                host: 'localhost',
            ),
            headers: new Headers(['Content-Type' => 'application/json']),
            cookies: [],
            serverParams: [],
            body: (new StreamFactory())->createStream(),
        );
        $this->response = new Response();
    }

    public function testStartCommand(UnitTester $I): void
    {
        $controller = new TelegramWebhookController(Stub::makeEmpty(LoggerInterface::class));
        $controller->__invoke($this->request, $this->response, $this->args);
    }
}
