<?php

use Silex\Application;

use Sce\RepoMan\Provider\Config as ConfigProvider;
use Sce\RepoMan\Provider\Log as LogProvider;
use Sce\RepoMan\Provider\Route as RouteProvider;
use Sce\RepoMan\Provider\ErrorHandler as ErrorHandlerProvider;
use Sce\RepoMan\Provider\GitRepoStore as GitRepoStoreProvider;
use Sce\RepoMan\Provider\ReportFactory as ReportFactoryProvider;
use Sce\RepoMan\Provider\ReportViewFactory as ReportViewFactoryProvider;
use Sce\RepoMan\Provider\CommandFactory as CommandFactoryProvider;

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
$app->register(new ReportViewFactoryProvider());

return $app;