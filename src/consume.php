<?php
/**
 * @author timrodger
 * Date: 05/12/15
 *
 * Consumes these events:
 *
 *  repo-mon.repository.activated
 *  repo-mon.repository.deactivated
 *  repo-mon.repository.added
 *  repo-mon.repository.removed
 *  repo-mon.update.scheduled - change to command.update.scheduled
 */

$app = require_once __DIR__ .'/app.php';
$app->boot();

$app['logger']->notice(sprintf("rabbit host: %s port: %s channel: %s\n",
    $app['config']->getRabbitHost(),
    $app['config']->getRabbitPort(),
    $app['config']->getRabbitChannelName()
    )
);

$updateHandler = function($event) use ($app) {
    // use $event['data']['full_name'] to get owner and url from this service

    $repository = $app['store']->get($event['data']['full_name']);

    if (!('1' === $repository['active'])){
        // log mis-configuration?
        return;
    }

    // check language & dependency manager are supported

    $token = $app['token-service']->getToken($repository['owner']);

    // publish a command
    $command = [
        'name' => 'command.repository.update',
        'data' => [
            'url' => $repository['url'],
            'token' => $token,
            'language' => $repository['lang'],
            'dependency_manager' => $repository['dependency_manager']
        ]
    ];

    $app['queue-client']->publish($command);

};

$addedHandler = function($event) use ($app) {

    $data = $event['data'];

    // should not overwrite existing data?
    $result = $app['store']->add(
        $data['url'],
        $data['full_name'],
        $data['owner'],
        $data['description'],
        $data['language'],
        $data['dependency_manager'],
        $data['timezone'],
        $active  = 0
    );
    echo " Result of add is '$result'\n";
};

$removedHandler = function ($event) use ($app) {
    $result = $app['store']->delete(
        $event['data']['full_name']
    );
    echo " Result of deactivate is '$result'\n";
};

$activatedHandler = function ($event) use ($app) {
    $result = $app['store']->activate(
        $event['data']['full_name']
    );
    echo " Result of deactivate is '$result'\n";
};

$deactivatedHandler = function ($event) use ($app) {
    $result = $app['store']->deactivate(
        $event['data']['full_name']
    );
    echo " Result of deactivate is '$result'\n";
};

$app['queue-client']->addEventHandler('repo-mon.repository.added', $addedHandler);
$app['queue-client']->addEventHandler('repo-mon.repository.activated', $activatedHandler);
$app['queue-client']->addEventHandler('repo-mon.repository.deactivated', $deactivatedHandler);
$app['queue-client']->addEventHandler('repo-mon.repository.removed', $removedHandler);
# $app['queue-client']->addEventHandler('repo-mon.update.scheduled', $updateHandler);

$app['queue-client']->consume();
