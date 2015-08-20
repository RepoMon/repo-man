<?php namespace Sce\RepoMan\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class ReportViewFactory implements ServiceProviderInterface
{
    public function register(Application $app){}

    public function boot(Application $app)
    {
        $app['report_view_factory'] = new \Sce\RepoMan\View\ReportViewFactory();
    }
}