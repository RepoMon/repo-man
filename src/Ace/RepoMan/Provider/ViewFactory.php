<?php namespace Ace\RepoMan\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class ViewFactory implements ServiceProviderInterface
{
    public function register(Application $app){}

    public function boot(Application $app)
    {
        $app['view_factory'] = new \Ace\RepoMan\View\ViewFactory();
    }
}
