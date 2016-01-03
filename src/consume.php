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

        // use $event['data']['full_name'] to get owner and url from this service

        $token = $app['token-service']->getToken($event['data']['owner']);

        // update the repository, locally using the token
        // pass the dependency manager and lang to the factory
        $command = $app['command_factory']->create(
            'dependencies/update/current',
            $event['data']['url'],
            $token
        );

        $command->execute();

    } else if ($event['name'] === 'repo-mon.repository.added') {

        $data = $event['data'];

        $active = 0;

        // should not overwrite existing data?
        $result = $app['store']->add(
            $data['url'],
            $data['full_name'],
            $data['owner'],
            $data['description'],
            $data['language'],
            $data['dependency_manager'],
            $data['timezone'],
            $active
        );
        echo " Result of add is '$result'\n";

    } else if ($event['name'] === 'repo-mon.repository.activated') {

        $result = $app['store']->activate(
            $event['data']['full_name']
        );
        echo " Result of activate is '$result'\n";

    } else if ($event['name'] === 'repo-mon.repository.deactivated') {

        $result = $app['store']->deactivate(
            $event['data']['full_name']
        );
        echo " Result of deactivate is '$result'\n";

    }
};

$app['queue-client']->consume($callback);
