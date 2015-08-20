<?php namespace Sce\RepoMan\Provider;

use SebastianBergmann\Exporter\Exception;
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

            if (!empty($url)) {
                $app['git_repo_store']->add($url);
                return $app->json(['status' => 'success', 'name' => $url]);
            } else {
                return $app->json(['status' => 'error'], 400);
            }

        });

        /**
         * Update all the configured repositories
         * Is this needed ? do we want to have users do this manually?
         * If we do support this it needs to be on a different endpoint
         */
        $app->post('/repositories/update', function(Request $req) use ($app){

            foreach ($app['git_repo_store']->getAll() as $repository) {
                $repository->update();
                $repository->checkout('master');
                $app['logger']->info("updated " . $repository->getUrl());
            }

            return new Response('', 200);

        });

        /**
         * Adds a token to use when authenticating with remote repository
         * $req should have a token and a host field
         */
        $app->post('/tokens', function(Request $req) use ($app){

            $host = $req->request->get('host');
            $token = $req->request->get('token');

            if (!empty($host) && !empty($token)) {
                $app['git_repo_store']->addToken($host, $token);
                return $app->json(['status' => 'success', 'host' => $host]);
            } else {
                return $app->json(['status' => 'error'], 400);
            }
        });

        $app->get('/report/{name}', function(Request $req, $name) use ($app) {

            $reporter = $app['report_factory']->create($name);

            // respond with the report output
            $result = $reporter->generate();

            // test result, if null return 200 and no body
            if (is_null($result)){
                return new Response('', 200, ['Content-Type' => 'text/plain']);
            }

            $view_name = 'report/' . $name;

            $view = $app['report_view_factory']->create($view_name, $req);

            $body = $view->render($result);

            // format based on request accept header
            return new Response($body, 200, ['Content-Type' => $view->getContentType()]);
        });

        /**
         * Update a repository's dependencies
         */
        $app->post('/dependencies', function(Request $req) use ($app) {

            $require = $req->get('require');
            $repository = $req->get('repository');

            $app['logger']->addInfo("require = '$require' repository='$repository'");

            $command = $app['command_factory']->create('dependencies/update', $repository);

            $command->execute(['require' => json_decode($require, true)]);

            return new Response(sprintf('Repository "%s" updated', $repository), 200);
        });
    }
}