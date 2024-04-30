<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2024-04-30
 * Time: 1:27
 */
require_once 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

$connection = new AMQPStreamConnection('192.168.1.158', 5672, 'admin', '123456');
$channel = $connection->channel();

$args = new AMQPTable(['x-delayed-type' => 'direct']);
$channel->exchange_declare('bonza', 'x-delayed-message', false, true, false, false, false, $args);

list($queue_name, , ) = $channel->queue_declare('', false, false, true, false);

$channel->queue_bind($queue_name, 'bonza', 'delay');

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($message) {
    echo ' [x] Received ', $message->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();