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
        /**
         * Respond with a JSON array of the repository names (urls)
         */
        $app->get('/repositories', function(Request $request) use ($app){

            $repositories = [];

            // for each repo generate the url to access it here
            foreach($app['git_repo_store']->getAll() as $repository) {
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

            $app['git_repo_store']->add($url);
            return $app->json(['status' => 'success', 'name' => $url]);

        })->before(function (Request $request, Application $app){

            $url = $request->request->get('url');

            if (empty($url)){
                $app->abort(400, json_encode(['error' => 'Url is missing']), ['Content-Type' => 'application/json']);
            }
        });

        /**
         * Update all the configured repositories
         * Is this needed ? do we want to have users do this manually?
         * If we do support this it needs to be on a different endpoint
         */
        $app->post('/repositories/update', function(Request $request) use ($app){

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
        $app->post('/tokens', function(Request $request) use ($app){

            $host = $request->request->get('host');
            $token = $request->request->get('token');

            $app['git_repo_store']->addToken($host, $token);
            return $app->json(['status' => 'success', 'host' => $host]);

        })->before(function (Request $request, Application $app) {
            $host = $request->request->get('host');
            $token = $request->request->get('token');

            if (empty($host) || empty($token)) {
                $app->abort(400, json_encode(['error' => 'Token and host are required']), ['Content-Type' => 'application/json']);
            }
        });

        /**
         * Generate a composer dependency report on the repositories
         */
        $app->get('/dependencies/report', function(Request $request) use ($app) {

            $report = $app['report_factory']->create('dependency/report');

            // respond with the report output
            $result = $report->generate();

            $view_name = 'dependency/report';

            $accept = $request->headers->get('Accept');
            $priorities = $app['view_factory']->getAvailableContentTypes($view_name);

            $negotiator = new FormatNegotiator();
            $type = $negotiator->getBest($accept, $priorities);
            $content_type = $type ? $type->getValue() : $priorities[0];

            $view = $app['view_factory']->create($view_name, $content_type);

            $body = $view->render($result);

            // format based on request accept header
            return new Response($body, 200, ['Content-Type' => $content_type]);
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
