<?php

namespace App\Shared\Providers;

use App\BotAi\Infrastructure\OpenAi\OpenAiApiService;
use App\BotAi\Infrastructure\Vk\VkApiService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenAiApiService::class, function (Application $app) {
            return new OpenAiApiService(
                $app->make(Client::class),
                config('common.openAi.token'),
            );
        });
        $this->app->singleton(VkApiService::class, function (Application $app) {
            return new VkApiService(
                $app->make(Client::class),
                config('common.vkBot.botToken'),
                config('common.vkBot.version'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
