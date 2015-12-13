<?php
/**
 * @author timrodger
 * Date: 05/12/15
 *
 * Consumes repo-mon.update.scheduled events
 * Updates repositories
 */

$app = require_once __DIR__ .'/app.php';
$app->boot();

$app['logger']->notice("rabbit host: %s port: %s channel: %s\n",
    $app['config']->getRabbitHost(),
    $app['config']->getRabbitPort(),
    $app['config']->getRabbitChannelName()
);

$store = $app['store'];

$callback = function($msg) use ($app) {

    $app['logger']->notice(" Received ", $msg->body);
    $event = json_decode($msg->body, true);

    if ($event['name'] === 'repo-mon.update.scheduled') {
        // update this repo
    }
};

$app['queue-client']->consume($callback);
