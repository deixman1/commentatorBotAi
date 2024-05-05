<?php
declare(strict_types=1);

namespace App\BotAi\Infrastructure\Vk;

use CURLFile;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class VkApiService
{
    public function __construct(
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
        $response = $this->httpClient->request('POST', 'https://api.vk.com/method/photos.getMessagesUploadServer', [
            'form_params' => [
                'access_token' => $this->botToken,
                'group_id' => 221612229,
                'v' => '5.131',
            ],
        ]);
        $response = json_decode($response->getBody()->getContents(), true);
        info('VK.RESPONSE', $response);
        return $response;
    }

    private function uploadImageOnServer(array $uploadServer, string $urlPhoto): array
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'vk_upload') . '.png';
        // Download the image
        // Download the image
        $imageContents = file_get_contents($urlPhoto);

        // Save the image to a temporary file
        file_put_contents($tempFile, $imageContents);

        // Create cURL file
        $file = new CURLFile($tempFile);

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $uploadServer['upload_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['photo' => $file]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL session and get the result
        $result = curl_exec($ch);

        // Close cURL session
        curl_close($ch);

        // Decode the result
        $decodedResult = json_decode($result, true);

        info('VK.RESPONSE', $decodedResult);
        return $decodedResult;
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
        info('VK.RESPONSE', $response);
        return $response;
    }
}
