<?php namespace Sce\Provider;

use Sce\Configuration;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * @author timrodger
 * Date: 23/06/15
 */
class Config implements ServiceProviderInterface
{
    /**
     * @var directory path
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