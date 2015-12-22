<?php namespace Ace\RepoMan\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Negotiation\FormatNegotiator;

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
         * Respond with a JSON array of the repository names (urls)
         */
        $app->get('/repositories', function(Request $request) use ($app){

            $repositories = [];

            // for each repo generate the url to access it here
            foreach($app['store']->getAll() as $repository) {
                $repositories[]= $repository->getUrl();
            }

            return $app->json($repositories);
        });

        /**
         * Adds a repository
         */
        $app->post('/repositories', function(Request $request) use ($app){

            // add the repo to the store
            $url = $request->request->get('url');

            $app['store']->add($url);
            return $app->json(['status' => 'success', 'name' => $url]);

        })->before(function (Request $request, Application $app){

            $url = $request->request->get('url');

            if (empty($url)){
                $app->abort(400, json_encode(['error' => 'Url is missing']), ['Content-Type' => 'application/json']);
            }
        });

//        /**
//         * Update all the configured repositories
//         * Is this needed ? do we want to have users do this manually?
//         * If we do support this it needs to be on a different endpoint
//         */
//        $app->post('/repositories/update', function(Request $request) use ($app){
//
//            foreach ($app['git_repo_store']->getAll() as $repository) {
//                $repository->update();
//                $repository->checkout('master');
//                $app['logger']->info("updated " . $repository->getUrl());
//            }
//
//            return new Response('', 200);
//
//        });

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
