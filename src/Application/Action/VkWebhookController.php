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
        $data = $this->request->getParsedBody();
        $this->logger->info('EVENT.VK', $data);
        if (isset($data['type']) && $data['type'] === 'confirmation') {
            return $this->getResponse('f00d5aaa');
        }
        $this->vkService->publishToQueue($this->request->getParsedBody());
        return $this->getResponse('ok');
    }

    private function getResponse(string $msg): ResponseInterface
    {
        $body = $this->response->getBody();
        $body->write($msg);
        return $this->response->withBody($body);
    }
}