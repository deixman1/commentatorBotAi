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
        $text = trim($text, ',');
        $text = trim($text);
        if (strncmp($text, "image", 5) === 0) {
            $text = str_replace([
                'image ',
                'image'
            ], '', $text);
            $response = $this->openAiApiService->executeGptDell($text);
            $this->logger->info('OpenAi', $response);
            foreach ($response['data'] as $url) {
                $this->sendMessage($peerId, $text, 'https://oaidalleapiprodscus.blob.core.windows.net/private/org-dejCuheg4txdwgfPsLvzAhzu/user-SLVKqOkI5U1eseE1ZLAxJLfs/img-yzZxnUdpYYZc529opG5xVyeJ.png?st=2023-12-28T15%3A14%3A12Z&se=2023-12-28T17%3A14%3A12Z&sp=r&sv=2021-08-06&sr=b&rscd=inline&rsct=image/png&skoid=6aaadede-4fb3-4698-a8f6-684d7786b067&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2023-12-28T15%3A19%3A26Z&ske=2023-12-29T15%3A19%3A26Z&sks=b&skv=2021-08-06&sig=bgyNCSKPB0WjWOfxbj1p7bPjo%2B7LiQaYT5v9MB7kH28%3D');
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