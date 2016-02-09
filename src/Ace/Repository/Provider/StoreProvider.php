<?php namespace Ace\Repository\Provider;

use Ace\Repository\Store\Memory;
use Ace\Repository\Store\RDBMSStoreFactory;
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
        $config = $app['config'];

        if ($config->getDbType() === 'MEMORY') {
            $app['store'] = new Memory();
        } else {
            $factory = new RDBMSStoreFactory(
                $config->getDbHost(),
                $config->getDbName(),
                $config->getDbUser(),
                $config->getDbPassword()
            );
            $app['store'] = $factory->create();
        }
    }
}
