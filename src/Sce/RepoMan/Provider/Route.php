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

        /**
         * Respond with a JSON array of the repository names (urls)
         */
        $app->get('/repositories', function(Request $req) use ($app){

            $repositories = $app['git_repo_collection']->getRepositories();

            $names = [];

            foreach($repositories as $repository) {
                $names [] = $repository->getUrl();
            }

            return $app->json($names);
        });

        /**
         * Adds a repository
         */
        $app->put('/repositories/{name}', function(Request $req, $name) use ($app){

            // add the repo to the store

            return $app->json(['status' => 'success', 'name' => $name]);

        })->assert('name', '.+');

        /**
         * Update all the configured repositories
         * Is this needed ? do we want to have users do this manually?
         * If we do support this it needs to be on a different endpoint
         */
        $app->post('/repositories/update', function(Request $req) use ($app){

            $repositories = $app['git_repo_collection']->getRepositories();

            foreach($repositories as $repository) {
                $repository->update();
            }

            return new Response('', 200);
        });
    }
}