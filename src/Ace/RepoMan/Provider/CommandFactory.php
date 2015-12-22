<?php namespace Ace\RepoMan\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * @author timrodger
 * Date: 07/06/15
 */
class CommandFactory implements ServiceProviderInterface
{
    public function register(Application $app){}

    public function boot(Application $app)
    {
        $app['command_factory'] = new \Ace\RepoMan\Command\CommandFactory($app['store']);
    }

}
