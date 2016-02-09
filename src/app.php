<?php

use Silex\Application;

use Ace\Repository\Provider\Config as ConfigProvider;
use Ace\Repository\Provider\Log as LogProvider;
use Ace\Repository\Provider\Route as RouteProvider;
use Ace\Repository\Provider\ErrorHandler as ErrorHandlerProvider;
use Ace\Repository\Provider\CommandFactory as CommandFactoryProvider;
use Ace\Repository\Provider\QueueClientProvider;
use Ace\Repository\Provider\TokenProvider;
use Ace\Repository\Provider\StoreProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$dir = '/tmp/repositories';

$app->register(new ErrorHandlerProvider());
$app->register(new StoreProvider());
$app->register(new ConfigProvider($dir));
$app->register(new LogProvider());
$app->register(new RouteProvider());
$app->register(new QueueClientProvider());

return $app;
