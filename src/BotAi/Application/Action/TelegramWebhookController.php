<?php
declare(strict_types=1);

namespace App\BotAi\Application\Action;

use App\BotAi\Domain\Telegram\TelegramService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TelegramService $telegramService,
        private readonly ResponseFactory $responseFactory,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->logger->info('EVENT.TELEGRAM', $request->all());
        $this->telegramService->webhookProcessing($request->all());
        return $this->responseFactory->json('ok');
    }
}
