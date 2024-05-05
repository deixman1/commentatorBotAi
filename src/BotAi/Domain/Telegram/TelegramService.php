<?php
declare(strict_types=1);

namespace App\BotAi\Domain\Telegram;

use App\BotAi\Infrastructure\Telegram\TelegramApiService;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class TelegramService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Environment $environment,
        private readonly TelegramApiService $telegramApiService,
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
        $this->telegramApiService->sendMessage(
            chatId: -803102340,
            text: $this->environment->render('message.twig', $renderData),
        );
    }
}
