<?php
/**
 * @author timrodger
 * Date: 05/12/15
 *
 * Consumes events
 * Updates repositories
 *
 */
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$channel_name = 'repo-mon.main';
$queue_host = 'rabbitmq';
$queue_port = 5672;

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler('/var/log/consume.log', Logger::DEBUG));
$logger->notice(sprintf(" rabbit host %s port %s\n", $queue_host, $queue_port));

$connection = new AMQPStreamConnection($queue_host, $queue_port, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare($channel_name, false, false, false, false);

echo ' Waiting for events. To exit press CTRL+C', "\n";

$callback = function($event) use ($logger) {
    $logger->notice(sprintf(" Received %s", $event->body));
};

$channel->basic_consume($channel_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();