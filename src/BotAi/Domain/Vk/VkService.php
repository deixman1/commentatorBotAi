<?php
declare(strict_types=1);

namespace App\BotAi\Domain\Vk;

use App\BotAi\Infrastructure\OpenAi\OpenAiApiService;
use App\BotAi\Infrastructure\Vk\VkApiService;
use Psr\Log\LoggerInterface;

class VkService
{
    public function __construct(
        private readonly VkApiService $vkApiService,
        private readonly OpenAiApiService $openAiApiService,
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
        $text = trim($text, ',');
        $text = trim($text);
        if (strncmp($text, "image", 5) === 0) {
            $text = str_replace([
                'image ',
                'image'
            ], '', $text);
            $response = $this->openAiApiService->executeGptDell($text);
            info('OpenAi', $response);
            foreach ($response['data'] as $url) {
                $this->sendMessage($peerId, $text, $url['url']);
            }
            return;
        }
        $response = $this->openAiApiService->executeGptTurbo($text);
        info('OpenAi', $response);
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
