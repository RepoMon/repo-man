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


        /**
         * Update a repository's dependencies
         * Either update the required versions or update the current versions
         */
        $app->post('/dependencies', function(Request $request) use ($app) {

            $require = $request->get('require', '');
            $repository = $request->get('repository');
            $owner = $request->get('owner');

            $token = $app['token-service']->getToken($owner);

            if (!empty($require)) {

                $app['logger']->addInfo("require = '$require' repository='$repository'");
                $command = $app['command_factory']->create('dependencies/update/required', $repository, $token);
                $command->execute(['require' => json_decode($require, true)]);

            } else {

                $app['logger']->addInfo("repository='$repository'");
                $command = $app['command_factory']->create('dependencies/update/current', $repository, $token);
                $command->execute(null);

            }

            return new Response(sprintf('Repository "%s" updated', $repository), 200);

        })->before(function (Request $request, Application $app) {

            $repository = $request->get('repository');

            if (empty($repository)) {
                $app->abort(400, json_encode(['error' => 'Repository is required']), ['Content-Type' => 'application/json']);
            }

            $owner = $request->get('owner');

            if (empty($owner)) {
                $app->abort(400, json_encode(['error' => 'Owner is required']), ['Content-Type' => 'application/json']);
            }
        });
    }
}
