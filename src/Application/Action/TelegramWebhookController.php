<?php
declare(strict_types=1);

namespace App\Application\Action;

use App\Domain\Telegram\TelegramService;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class TelegramWebhookController extends AbstractController
{
    public function __construct(
        LoggerInterface $logger,
        private readonly TelegramService $telegramService,
    )
    {
        parent::__construct($logger);
    }

    protected function execute(): ResponseInterface
    {
        $this->logger->info('EVENT.TELEGRAM', $this->request->getParsedBody());
        $this->telegramService->webhookProcessing($this->request->getParsedBody());
        $body = $this->response->getBody();
        $body->write('ok');
        return $this->response->withBody($body);
    }
}