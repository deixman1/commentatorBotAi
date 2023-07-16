<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Domain\Vk\VkService;
use App\Infrastructure\RabbitMq\MessageBus;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('vk-consumer')]
class VkConsumer extends Command
{
    public function __construct(
        private readonly MessageBus $messageBus,
        private readonly VkService $vkService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->messageBus->bindRoutePatternToQueue('vk', 'vk');
        $this->messageBus->receive('vk', function (AMQPMessage $msg) {
            $parsedData = json_decode($msg->getBody(), true);
            $this->vkService->webhookProcessing($parsedData);
        });
        return self::SUCCESS;
    }
}
