<?php

namespace Bonza\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQClient
{
    protected $connection;
    protected $channel;

    public function __construct($host, $port, $user, $password, $vhost = '/')
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $this->channel = $this->connection->channel();
    }

    public function send($queue, $message)
    {
        $this->channel->basic_publish(new AMQPMessage($message), '', $queue);
    }

    public function receive($queue, $callback)
    {
        $this->channel->queue_declare($queue, false, false, false, false);
        $this->channel->basic_consume($queue, '', false, true, false, false, function ($message) use ($callback){
             $callback($message->body);
        });
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}