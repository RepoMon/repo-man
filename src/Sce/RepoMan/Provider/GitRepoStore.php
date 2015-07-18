<?php namespace Sce\RepoMan\Provider;

use Predis\Client;
use Sce\RepoMan\Git\MemoryStore;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Sce\RepoMan\Git\Store;

/**
 * @author timrodger
 * Date: 17/07/15
 */
class GitRepoStore implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        // instantiate a different store depending on the value of $app['config']->getStoreDsn()
        $dsn = $app['config']->getStoreDsn();

        if ('MEMORY' == $dsn) {
            $store = new MemoryStore($app['config']);
        } else if ('UNAVAILABLE' == $dsn) {
            //return new UnavailableStore();
        } else {
            $store = new Store($app['config'], new Client($dsn));
        }

        $app['git_repo_store'] = $store;
    }
}