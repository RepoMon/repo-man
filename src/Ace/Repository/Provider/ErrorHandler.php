<?php namespace Ace\Repository\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Handles exceptions by returning responses with a message
 */
class ErrorHandler implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $app->error(function (Exception $e) use($app) {
            $app['logger']->addError($e->getMessage());
            $status = ($e->getCode() > 99) ? $e->getCode() : 500;
            return new Response($e->getMessage(), $status);
        });

    }
}
