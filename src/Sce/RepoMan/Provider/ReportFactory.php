<?php namespace Sce\RepoMan\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * @author timrodger
 * Date: 07/06/15
 */
class ReportFactory implements ServiceProviderInterface
{
    public function register(Application $app){}

    public function boot(Application $app)
    {
        $app['report_factory'] = new \Sce\RepoMan\Report\ReportFactory($app['git_repo_store']);
    }

}