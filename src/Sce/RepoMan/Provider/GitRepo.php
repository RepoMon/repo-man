<?php namespace Ace\RepoMan\Provider;

/**
 * @author timrodger
 * Date: 17/07/15
 */
class GitRepo
{
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $app['git_repo_collection'] = new GitRepoCollection(
            $app['config']
        );
    }
}