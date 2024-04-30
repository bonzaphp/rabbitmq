<?php

namespace Bonza\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class DelayedMessageProducer
{
    protected $connection;
    protected $channel;

    public function __construct($host, $port, $user, $password, $vhost = '/')
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $this->channel = $this->connection->channel();
    }

//    public function sendDelayed($queue, $message, $delay)
//    {
//        // Create a message with the delay in the properties
//        $delayMessage = new AMQPMessage($message);
//        $delayMessage->set('x-delay', $delay * 1000); // Delay in milliseconds
//
//        // Send the message to the queue
//        $this->channel->basic_publish($delayMessage, '', $queue);
//    }

    public function sendDelayed($exchangeName, $routingKey, $message, $delay)
    {
        // Enable the delayed message plugin if it's not already enabled
//        if (!$this->isPluginEnabled('rabbitmq_delayed_message_exchange')) {
//            $this->channel->exchange_declare("", AMQPExchangeType::DIRECT, false, true, false);
//            $this->channel->plugin_enable('rabbitmq_delayed_message_exchange');
//        }
        $args = new AMQPTable(['x-delayed-type' => 'direct']);
        // 定义一个延迟交换机
//        $this->channel->exchange_declare($exchangeName, AMQPExchangeType::X_DELAYED_MESSAGE, false, true, false);
        $this->channel->exchange_declare($exchangeName, 'x-delayed-message', false, true, false,false,false,$args);

        // Declare a queue and bind it to the delayed exchange
        $this->channel->queue_declare("ok1", false, true, false, false);
        $this->channel->queue_bind("ok1", $exchangeName, $routingKey);

        // Set the message properties
//        $msg = new AMQPMessage($message, array('delivery_mode' => 2)); // Make message persistent
        $msg = new AMQPMessage($message, ['application_headers' => new AMQPTable(['x-delay'=>$delay * 1000 ])]); // Make message persistent
//        $msg->set('x-delay', $delay * 1000); // Delay in milliseconds


        // Publish the message to the delayed exchange
        $this->channel->basic_publish($msg, $exchangeName, $routingKey,false,false);
    }

    protected function isPluginEnabled($pluginName)
    {
        $list = $this->channel->list_plugins();
        foreach ($list as $plugin) {
            if ($plugin['name'] == $pluginName) {
                return true;
            }
        }
        return false;
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}