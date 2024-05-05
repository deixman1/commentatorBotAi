<?php

return [
    'openAi' => [
        'token' => env('OPENAI_TOKEN'),
    ],
    'telegramBot' => [
        'botToken' => env('TELEGRAM_BOT_TOKEN'),
        'chatId' => env('TELEGRAM_BOT_CHAT_ID'),
    ],
    'vkBot' => [
        'version' => env('VK_VERSION'),
        'botToken' => env('VK_BOT_TOKEN'),
    ],
];
