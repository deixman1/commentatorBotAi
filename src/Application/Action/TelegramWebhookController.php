<?php
declare(strict_types=1);

namespace App\Application\Action;

use Psr\Http\Message\ResponseInterface;

class TelegramWebhookController extends AbstractController
{
    protected function execute(): ResponseInterface
    {
        $this->logger->info('EVENT', $this->request->getParsedBody());
        $body = $this->response->getBody();
        $body->write('ok');
        return $this->response->withBody($body);
    }
}