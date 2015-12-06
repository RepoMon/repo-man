<?php namespace Ace\RepoMan\Provider;

use Predis\Client;
use Ace\RepoMan\Store\Memory as MemoryStore;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Ace\RepoMan\Store\Redis as RedisStore;

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
            $store = new RedisStore($app['config'], new Client($dsn));
        }

        $app['git_repo_store'] = $store;
    }
}
