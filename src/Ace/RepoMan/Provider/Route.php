<?php namespace Ace\RepoMan\Provider;

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
        /**
         * Respond with a JSON array of the repositories owned by owner query parameter
         */
        $app->get('/repositories', function(Request $request) use ($app){

            $owner = $request->query->get('owner');

            return $app->json(
                $app['store']->getAll($owner)
            );
        });

        /**
         * return the information for the named repository
         */
        $app->get('/repositories/{vendor}/{library}', function(Request $request, $vendor, $library) use ($app){

            return $app->json(
                $app['store']->get("$vendor/$library")
            );
        });
    }
}
