<?php
declare(strict_types=1);

namespace App\Domain\Vk;

use App\Infrastructure\Vk\VkApiService;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class VkService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Environment $environment,
        private readonly VkApiService $vkApiService,
    )
    {
    }

    /**
     * todo $parsedData to DTO
     * @param array $parsedData
     * @return void
     */
    public function webhookProcessing(array $parsedData): void
    {
        $this->sendMessage(['data' => json_encode($parsedData)]);
    }

    private function sendMessage(array $renderData): void
    {
        $this->vkApiService->sendMessage(
            peerId: 2000000002,
            text: $this->environment->render('message.twig', $renderData),
        );
    }
}