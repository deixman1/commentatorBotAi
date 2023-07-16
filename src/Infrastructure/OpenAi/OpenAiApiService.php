<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAi;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class OpenAiApiService
{
    public function __construct(
        private readonly Client $httpClient,
        private readonly string $openAiToken,
    )
    {
    }

    public function completions(string $userMsg): array
    {
        $bodyParams = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $userMsg]],//Say this is a test!
        ];
        $request = new Request(
            method: 'POST',
            uri: 'https://api.openai.com/v1/chat/completions',
            headers: [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->openAiToken
            ],
            body: json_encode($bodyParams, 256)
        );
        return json_decode($this->httpClient->sendRequest($request)->getBody()->getContents(), true);
    }
}