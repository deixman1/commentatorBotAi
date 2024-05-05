<?php

declare(strict_types=1);

namespace App\BotAi\Infrastructure\RabbitMq;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageBus
{
    private string $exchangeName;
    private ?AMQPStreamConnection $connection;
    private ?AMQPChannel $channel;
    private array $queueInits;
    private bool $isConsuming;

    public function __construct(string $projectName, string $uri, bool $exchangeDurable = true, bool $exchangeAutoDelete = false)
    {
        $this->exchangeName = $projectName;
        $this->connection = null;
        $this->channel = null;
        $this->queueInits = [];
        $this->initBroker($uri, $exchangeDurable, $exchangeAutoDelete);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): void
    {
        if ($this->channel !== null) {
            $this->channel->close();
        }
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }

    public function send(string $routingKey, AMQPMessage $message): void
    {
        $this->channel->basic_publish($message, $this->exchangeName, $routingKey);
    }

    public function bindRoutePatternToQueue(string $routePattern, string $queueName, bool $addProjectToQueueName = true, bool $tryInitQueue = true): void
    {
        if ($addProjectToQueueName) {
            $queueName = $this->getQueueName($queueName);
        }
        if ($tryInitQueue) {
            $this->initQueue($queueName);
        }
        $this->channel->queue_bind($queueName, $this->exchangeName, $routePattern);
    }

    public function receive(string $fromQueue, callable $callback, bool $no_acknowledgement = true, bool $addProjectToQueueName = true): void
    {
        $this->isConsuming = true;

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(
            $addProjectToQueueName ? $this->getQueueName($fromQueue) : $fromQueue,
            '',
            false,
            false,
            false,
            false,
            function(AMQPMessage $msg) use ($callback, $no_acknowledgement) {
                if ($no_acknowledgement) {
                    $msg->ack(false);
                }
                return $callback($msg);
            }
        );

        while ($this->channel->is_consuming() && $this->isConsuming) {
            $this->channel->wait();
        }
    }

    private function initBroker(string $uri, bool $exchangeDurable, bool $exchangeAutoDelete): void
    {
        if ($this->connection === null) {
            [$login, $password, $host, $port, $vhost] = $this->parseUri($uri);
            $this->connection = new AMQPStreamConnection($host, $port, $login, $password, $vhost);
            $this->channel = $this->connection->channel();
            $this->channel->exchange_declare($this->exchangeName, 'topic', false, $exchangeDurable, $exchangeAutoDelete);
        }
    }

    private function initQueue(string $queueName): void
    {
        if (!isset($this->queueInits[$queueName])) {
            $data = $this->channel->queue_declare($queueName, false, true, false, false);
            if ($data) {
                $this->queueInits[$queueName] = $data;
            }
        }
    }

    public function stopConsuming(): void
    {
        $this->isConsuming = false;
    }

    private function initTemporaryQueue(): string
    {
        [$queueName,,] = $this->channel->queue_declare('', false, false, true, false);
        return $queueName;
    }

    private function parseUri(string $uri): array
    {
        $matches = [];
        preg_match('|^tcp://(.*?):(.*?)@(.*?):(\d+)/(.*?)$|', $uri, $matches);
        if (count($matches) < 5) {
            throw new ParseUriException($uri);
        }

        return [
            $login = (string)$matches[1],
            $password = (string)$matches[2],
            $host = (string)$matches[3],
            $port = (int)$matches[4],
            $vhost = (string)($matches[5] ?: '/'),
        ];
    }

    private function getQueueName(string $fromQueue): string
    {
        return $this->exchangeName . '.' . $fromQueue;
    }
}
