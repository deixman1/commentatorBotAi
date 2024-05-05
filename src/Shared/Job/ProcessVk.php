<?php

namespace App\Shared\Job;

use App\BotAi\Domain\Vk\VkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $message;
    /**
     * Create a new job instance.
     */
    public function __construct(
        array $data,
    )
    {
        $this->message = $data;
    }

    /**
     * Execute the job.
     * Параметры для метода передаются через DI
     */
    public function handle(VkService $vkService): void
    {
        $vkService->webhookProcessing($this->message);
    }
}
