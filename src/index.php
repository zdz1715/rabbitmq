<?php
require_once '../autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;


spl_autoload_register('\Rabbitmq\Autoloader::loadByNamespace');

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('delay_exchange', 'direct',false,false,false);
$channel->exchange_declare('cache_exchange', 'direct',false,false,false);

$tale = new AMQPTable();
$tale->set('x-dead-letter-exchange', 'delay_exchange');
$tale->set('x-dead-letter-routing-key','delay_exchange');
//$tale->set('x-message-ttl',60000);

$channel->queue_declare('cache_queue',false,true,false,false,false,$tale);
$channel->queue_bind('cache_queue', 'cache_exchange','cache_exchange');

$channel->queue_declare('delay_queue',false,true,false,false,false);
$channel->queue_bind('delay_queue', 'delay_exchange','delay_exchange');


$msg = new AMQPMessage('Hello World'.$argv[1],array(
    'expiration' => intval($argv[1]),
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT

));

$channel->basic_publish($msg,'cache_exchange','cache_exchange');
echo date('Y-m-d H:i:s')." [x] Sent 'Hello World!' ".PHP_EOL;

$channel->close();
$connection->close();