<?php namespace Ace\RepoMan\Provider;

use Ace\RepoMan\Store\Memory;
use Ace\RepoMan\Store\RDBMSStoreFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * @author timrodger
 * Date: 23/06/15
 */
class StoreProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {

    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
        if (getenv('DB_TYPE') === 'MEMORY') {
            $app['store'] = new Memory();
        } else {
            $config = $app['config'];

            $factory = new RDBMSStoreFactory(
                $config->getDbHost(),
                $config->getDbName(),
                $config->getDbUser(),
                $config->getDbPassword(),
                'dir'
            );
            $app['store'] = $factory->create();
        }
    }
}
