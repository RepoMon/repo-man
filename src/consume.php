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

$app['logger']->notice(sprintf("rabbit host: %s port: %s channel: %s\n",
    $app['config']->getRabbitHost(),
    $app['config']->getRabbitPort(),
    $app['config']->getRabbitChannelName()
    )
);

$callback = function($msg) use ($app) {

    $app['logger']->notice(" Received " . $msg->body);
    $event = json_decode($msg->body, true);

    if ($event['name'] === 'repo-mon.update.scheduled') {
        // update this repo
        // get token
        $token = $app['token-service']->getToken($event['data']['owner']);
        // update the repository, locally using the token
        $command = $app['command_factory']->create('dependencies/update/current', $event['data']['url']);
        $command->execute(null);

    }
};

$app['queue-client']->consume($callback);
