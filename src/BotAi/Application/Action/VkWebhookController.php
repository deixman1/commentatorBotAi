<?php
declare(strict_types=1);

namespace App\BotAi\Application\Action;

use App\Shared\Http\Controllers\Controller;
use App\Shared\Job\ProcessVk;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class VkWebhookController extends Controller
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ResponseFactory $responseFactory,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->all();
        $this->logger->info('EVENT.VK', $data ?: []);
        if (isset($data['type']) && $data['type'] === 'confirmation') {
            return $this->responseFactory->json('7ab1dc70');
        }
        if ($data) {
            ProcessVk::dispatch($request->all());
        }
        return $this->responseFactory->json('ok');
    }
}
