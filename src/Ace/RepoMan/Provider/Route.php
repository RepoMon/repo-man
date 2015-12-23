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
         * Respond with a JSON array of the repositories
         */
        $app->get('/repositories/{owner}', function(Request $request, $owner) use ($app){

            return $app->json(
                $app['store']->getAll($owner)
            );
        });

        /**
         * Update a repository's dependencies
         * Either update the required versions or update the current versions
         */
        $app->post('/dependencies', function(Request $request) use ($app) {

            $require = $request->get('require', '');
            $repository = $request->get('repository');

            if (!empty($require)) {

                $app['logger']->addInfo("require = '$require' repository='$repository'");
                $command = $app['command_factory']->create('dependencies/update/required', $repository);
                $command->execute(['require' => json_decode($require, true)]);

            } else {

                $app['logger']->addInfo("repository='$repository'");
                $command = $app['command_factory']->create('dependencies/update/current', $repository);
                $command->execute(null);

            }

            return new Response(sprintf('Repository "%s" updated', $repository), 200);

        })->before(function (Request $request, Application $app) {

            $repository = $request->get('repository');

            if (empty($repository)) {
                $app->abort(400, json_encode(['error' => 'Repository is required']), ['Content-Type' => 'application/json']);
            }
        });
    }
}
