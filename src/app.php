<?php

use Silex\Application;

use Ace\RepoMan\Provider\Config as ConfigProvider;
use Ace\RepoMan\Provider\Log as LogProvider;
use Ace\RepoMan\Provider\Route as RouteProvider;
use Ace\RepoMan\Provider\ErrorHandler as ErrorHandlerProvider;
use Ace\RepoMan\Provider\GitRepoStore as GitRepoStoreProvider;
use Ace\RepoMan\Provider\ReportFactory as ReportFactoryProvider;
use Ace\RepoMan\Provider\ViewFactory as ViewFactoryProvider;
use Ace\RepoMan\Provider\CommandFactory as CommandFactoryProvider;
use Ace\RepoMan\Provider\QueueClientProvider;
use Ace\RepoMan\Provider\TokenProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$dir = '/tmp/repositories';

$app->register(new ConfigProvider($dir));
$app->register(new LogProvider());
$app->register(new ErrorHandlerProvider());
$app->register(new RouteProvider());
$app->register(new GitRepoStoreProvider());
$app->register(new ReportFactoryProvider());
$app->register(new CommandFactoryProvider());
$app->register(new ViewFactoryProvider());
$app->register(new QueueClientProvider());
$app->register(new TokenProvider());

return $app;
