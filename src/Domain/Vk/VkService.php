<?php
declare(strict_types=1);

namespace App\Domain\Vk;

use App\Infrastructure\OpenAi\OpenAiApiService;
use App\Infrastructure\RabbitMq\MessageBus;
use App\Infrastructure\Vk\VkApiService;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class VkService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Environment $environment,
        private readonly VkApiService $vkApiService,
        private readonly OpenAiApiService $openAiApiService,
        private readonly MessageBus $messageBus,
    )
    {
    }

    /**
     * @param array $parsedData
     * @return void
     */
    public function publishToQueue(array $parsedData): void
    {
        $this->messageBus->send('vk', new AMQPMessage(json_encode($parsedData)));
    }

    /**
     * todo $parsedData to DTO
     * @param array $parsedData
     * @return void
     */
    public function webhookProcessing(array $parsedData): void
    {
        if ($parsedData['type'] !== 'message_new') {
            return;
        }
        $text = $parsedData['object']['message']['text'];
        $peerId = $parsedData['object']['message']['peer_id'];
        if (!str_contains($text, '@club221612229')
            && !str_contains($text, '@botcommentai')
            && !str_contains($text, '@public221612229')
            && !str_contains($text, '[club221612229|@botcommentai]')
            && !str_contains($text, '[club221612229|@public221612229]')) {
            return;
        }
        $text = str_replace([
            '[club221612229|@botcommentai]',
            '[club221612229|@public221612229]',
            '@botcommentai',
            '@public221612229',
            '@club221612229'
        ], '', $text);
        $text = trim($text);
        if (strncmp($text, "image", 5) === 0) {
            $text = str_replace([
                'image ',
                'image'
            ], '', $text);
            $response = $this->openAiApiService->executeGptDell($text);
            $this->logger->info('OpenAi', $response);
            foreach ($response['data'] as $url) {
                $this->sendMessage($peerId, $text, $url['url']);
            }
            return;
        }
        $response = $this->openAiApiService->executeGptTurbo($text);
        $this->logger->info('OpenAi', $response);
        foreach ($response['choices'] as $choice) {
            $this->sendMessage($peerId, $choice['message']['content']);
        }
    }

    private function sendMessage(int $peerId, string $msg, ?string $urlPhoto = null): void
    {
        $this->vkApiService->sendMessage(
            peerId: $peerId,
            text: $msg,
            urlPhoto: $urlPhoto,
        );
    }
}