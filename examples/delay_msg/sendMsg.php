<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2024-04-29
 * Time: 19:31
 */
require_once dirname(__DIR__).'/../vendor/autoload.php';


$rabbit = new Bonza\RabbitMQ\DelayedMessageProducer('192.168.1.158',5672,'admin','123456');

$rabbit->sendDelayed('ok_yang','delay','你来了吗',10);

$rabbit->close();