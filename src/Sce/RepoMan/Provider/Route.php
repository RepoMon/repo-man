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
            return new Response('RepoMan', 200);
        });

        /**
         * Respond with a JSON array of the repository names (urls)
         */
        $app->get('/repositories', function(Request $req) use ($app){

            $repositories = [];

            // for each repo generate the url to access it here
            foreach($app['git_repo_store']->getAll() as $repository) {
                $repositories[$repository->getId()] = $repository->getUrl();
            }

            return $app->json($repositories);
        });

        /**
         * Adds a repository
         */
        $app->post('/repositories', function(Request $req) use ($app){

            // add the repo to the store
            $url = $req->request->get('url');
            $app['git_repo_store']->add($url);
            return $app->json(['status' => 'success', 'name' => $url]);

        })->assert('name', '.+');

        /**
         * Update all the configured repositories
         * Is this needed ? do we want to have users do this manually?
         * If we do support this it needs to be on a different endpoint
         */
        $app->post('/repositories/update', function(Request $req) use ($app){

            foreach($app['git_repo_store']->getAll() as $repository) {
                $repository->update();
            }

            return new Response('', 200);
        });
    }
}