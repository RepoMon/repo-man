<?php namespace Sce\RepoMan\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Sce\RepoMan\View\ReportViewFactory;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class ReportViewFactory implements ServiceProviderInterface
{
    public function register(Application $app){}

    public function boot(Application $app)
    {
        $app['report_view_factory'] = new ReportViewFactory();
    }
}