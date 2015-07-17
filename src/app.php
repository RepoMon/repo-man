<?php

use Silex\Application;

use Sce\Provider\Config as ConfigProvider;
use Sce\Provider\Twig as TwigProvider;
use Sce\Provider\Log as LogProvider;
use Sce\Provider\Route as RouteProvider;
use Sce\Provider\ErrorHandler as ErrorHandlerProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(new ConfigProvider(__DIR__));
//$app->register(new LogProvider());
//$app->register(new ErrorHandlerProvider());
//$app->register(new TwigProvider(__DIR__));
$app->register(new RouteProvider());

return $app;