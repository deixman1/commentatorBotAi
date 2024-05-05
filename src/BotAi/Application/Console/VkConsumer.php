<?php

declare(strict_types=1);

namespace App\BotAi\Application\Console;

use App\BotAi\Domain\Vk\VkService;
use App\BotAi\Infrastructure\RabbitMq\MessageBus;
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

    }
}
