<?php
declare(strict_types=1);

namespace App\Application\Action;

use App\Domain\Vk\VkService;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class VkWebhookController extends AbstractController
{
    public function __construct(
        LoggerInterface $logger,
        private readonly VkService $vkService,
    )
    {
        parent::__construct($logger);
    }

    protected function execute(): ResponseInterface
    {
        $this->logger->info('EVENT.VK', $this->request->getParsedBody());
        $this->vkService->webhookProcessing($this->request->getParsedBody());
        $body = $this->response->getBody();
        $body->write('dcf854f7');
        return $this->response->withBody($body);
    }
}