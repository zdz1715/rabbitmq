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

$channel->queue_bind($queue1, $exchange1);

echo ' [*] Waiting for message. To exit press CTRL+C '.PHP_EOL;

$callback = function ($msg){
    echo date('Y-m-d H:i:s')." [x] Received",$msg->body,PHP_EOL;

    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

};

//只有consumer已经处理并确认了上一条message时queue才分派新的message给它
$channel->basic_qos(null, 1, null);
$channel->basic_consume($queue1,'',false,false,false,false,$callback);


while (count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
