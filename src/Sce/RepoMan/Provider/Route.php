<?php namespace Sce\RepoMan\Provider;

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

            $result = true;

            foreach($app['git_repo_store']->getAll() as $repository) {
                if (!$repository->update()){
                    $result = false;
                }
            }

            if ($result) {
                return new Response('', 200);
            } else {
                return new Response('', 500);
            }

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

        /**
         * Generate a composer dependency report on the repositories
         */
        $app->get('/reports/dependency/composer', function(Request $req) use ($app){

            $report = $app['report_factory']->create('dependency/composer');

            // respond with the report output
            $result = $report->generate();

            $accept = $req->headers->get('Accept');
            $priorities = ['application/json', 'text/html', 'text/csv'];

            $negotiator = new FormatNegotiator();
            $type = $negotiator->getBest($accept, $priorities);
            $value = $type ? $type->getValue() : '';
            $view = $app['view_factory']->create('dependency/composer', $value);

            $body = $view->render($result);

            // format based on request accept header
            return new Response($body, 200, ['Content-Type' => $type]);

        });

        /**
         * Add an endpoint to update a repositories dependencies
         */
        $app->post('/dependencies/composer', function(Request $req) use ($app){

            $require = $req->get('require');
            $repository = $req->get('repository');

            $app['logger']->addInfo("require = '$require' repository='$repository'");

            $command = $app['command_factory']->create('dependencies/composer', $repository);

            $result = $command->execute(['require' => json_decode($require, true)]);

            if ($result) {
                return new Response('Repository updated', 200);
            } else {
                return new Response('Executing command failed', 500);
            }
        });
    }
}