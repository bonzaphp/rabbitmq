<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2024-04-29
 * Time: 19:31
 */

require_once 'vendor/autoload.php';

$rabbit = new Bonza\RabbitMQ\DelayedMessageProducer('192.168.1.158',5672,'admin','123456');

$rabbit->sendDelayed('bonza','delay','深圳市牧翼电子商务有限公司（可查询交易及商户）--快付内管系统后台：
登录域名：https://admin.yunfastpay.com/quickpay-admin/index.html
帐号:13662699916
密码:123456',10);

$rabbit->close();