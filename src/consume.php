<?php
/**
 * @author timrodger
 * Date: 05/12/15
 *
 * Consumes these events:
 *
 *  repo-mon.update.scheduled
 *  repo-mon.repo.configured
 *  repo-mon.repo.unconfigured
 *
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

        $token = $app['token-service']->getToken($event['data']['owner']);

        // update the repository, locally using the token
        $command = $app['command_factory']->create(
            'dependencies/update/current',
            $event['data']['url'],
            $token
        );

        $command->execute();

    } else if ($event['name'] === 'repo-mon.repo.configured') {

        $result = $app['store']->add(
            $event['data']['url'],
            $event['data']['owner'],
            $event['data']['language'],
            $event['data']['dependency_manager']
        );
        echo " Result of insert is '$result'\n";

    } else if ($event['name'] === 'repo-mon.repo.unconfigured') {

        $result = $app['store']->delete($event['data']['url']);
        echo " Result of delete is '$result'\n";
}
};

$app['queue-client']->consume($callback);
