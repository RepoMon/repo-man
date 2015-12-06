<?php namespace Ace\RepoMan\Provider;

use Ace\RepoMan\Configuration;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * @author timrodger
 * Date: 23/06/15
 */
class Config implements ServiceProviderInterface
{
    /**
     * @var string directory path
     */
    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function register(Application $app)
    {
        $app['config'] = new Configuration($this->dir);
    }

    public function boot(Application $app)
    {
    }
}
