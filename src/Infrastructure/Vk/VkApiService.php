<?php
declare(strict_types=1);

namespace App\Infrastructure\Vk;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerInterface;

class VkApiService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Client $httpClient,
        private readonly string $botToken,
        private readonly string $version,
    )
    {
    }

    public function sendMessage(int $peerId, string $text, ?string $urlPhoto = null): void
    {
        for ($i = 0; $i <= mb_strlen($text); $i += 4096) {
            $bodyParams = [
                'random_id' => 0,
                'peer_id' => $peerId,
                'message' => mb_substr($text, $i, 4096),
                'v' => $this->version,
            ];
            if ($urlPhoto) {
                $photo = $this->uploadImage($peerId, $urlPhoto);
                $bodyParams['attachment'] = 'photo' . $photo['response'][0]['owner_id'] . '_' . $photo['response'][0]['id'];
            }
            $request = new Request(
                method: 'POST',
                uri: 'https://api.vk.com/method/messages.send?' . http_build_query($bodyParams),
                headers: [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->botToken
                ],
                body: json_encode($bodyParams, 256)
            );
            $this->httpClient->sendRequest($request);
        }
    }

    private function uploadImage(int $peerId, string $urlPhoto): array
    {
        return $this->saveMessagesPhoto(
            $this->uploadImageOnServer(
                $this->getMessagesUploadServer($peerId)['response'],
                $urlPhoto
            )
        );
    }

    private function getMessagesUploadServer(int $peerId): array
    {
        $bodyParams = [
            'peer_id' => $peerId,
            'v' => $this->version,
            'access_token' => $this->botToken,
        ];
        $request = new Request(
            method: 'POST',
            uri: 'https://api.vk.com/method/photos.getMessagesUploadServer',
            headers: [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            body: json_encode($bodyParams, 256)
        );
        $response = json_decode($this->httpClient->sendRequest($request)->getBody()->getContents(), true);
        $this->logger->info('VK.RESPONSE', $response);
        return $response;
    }

    private function uploadImageOnServer(array $uploadServer, string $urlPhoto): array
    {
        $response = $this->httpClient->request('POST', $uploadServer['upload_url'], [
            'multipart' => [
                [
                    'name' => 'photo',
                    'contents' => fopen($urlPhoto, 'r'),
                ],
            ],
        ]);

        $response = json_decode($response->getBody()->getContents(), true);
        $this->logger->info('VK.RESPONSE', $response);
        return $response;
    }

    private function saveMessagesPhoto(array $uploadedPhoto): array
    {
        $response = $this->httpClient->request('POST', 'https://api.vk.com/method/photos.saveMessagesPhoto', [
            'multipart' => [
                [
                    'name' => 'photo',
                    'contents' => $uploadedPhoto['photo'],
                ],
                [
                    'name' => 'server',
                    'contents' => $uploadedPhoto['server'],
                ],
                [
                    'name' => 'hash',
                    'contents' => $uploadedPhoto['hash'],
                ],
                [
                    'name' => 'v',
                    'contents' => $this->version,
                ],
                [
                    'name' => 'access_token',
                    'contents' => $this->botToken,
                ],
            ],
        ]);

        $response = json_decode($response->getBody()->getContents(), true);
        $this->logger->info('VK.RESPONSE', $response);
        return $response;
    }
}