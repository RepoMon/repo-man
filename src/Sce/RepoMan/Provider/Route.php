<?php namespace Sce\RepoMan\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Configures routing
 */
class Route implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $app->get("/", function(Request $req) use ($app){
            return new Response('hello', 200);
        });
    }
}