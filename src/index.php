<?php
require_once '../autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;


spl_autoload_register('\Rabbitmq\Autoloader::loadByNamespace');

$queue = 'msg';
$queue1 = 'msg_1';
$exchange = 'router';
$exchange1 = 'router_1';
$consumerTag = 'consumer';

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest', '/');
$channel = $connection->channel();



$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, false, false);
$channel->exchange_declare($exchange1, AMQPExchangeType::DIRECT, false, false, false);


$tale = new AMQPTable();
$tale->set('x-dead-letter-exchange', $exchange1);


$channel->queue_declare($queue, false, true, false, false);
$channel->queue_bind($queue, $exchange);


$channel->queue_declare($queue1, false, true, false, false);
$channel->queue_bind($queue1, $exchange1);


$msg = new AMQPMessage('Hello World'. mt_rand(1,30000),array(
    'expiration' => intval(18000),
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
));


$channel->basic_publish($msg,$exchange, $exchange);
echo date('Y-m-d H:i:s')." [x] Sent 'Hello World!' ".PHP_EOL;

$channel->close();
$connection->close();