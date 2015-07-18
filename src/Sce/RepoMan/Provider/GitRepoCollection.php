<?php namespace Sce\RepoMan\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Sce\RepoMan\Git\RepositoryCollection as Collection;

/**
 * @author timrodger
 * Date: 17/07/15
 */
class GitRepoCollection implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $app['git_repo_collection'] = new Collection(
            $app['config']
        );
    }
}