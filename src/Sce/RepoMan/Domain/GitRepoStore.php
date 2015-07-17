<?php namespace Sce\RepoMan\Domain;

use Sce\RepoMan\Configuration;

/**
 * @author timrodger
 * Date: 17/07/15
 */
class GitRepoStore
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var array of GitRepo instances
     */
    private $repositories = [];

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @return array of GitRepo instances
     */
    public function getRepositories()
    {
        if (count($this->repositories)){
            return $this->repositories;
        }

        // get the repos from redis

        return $this->repositories;
    }
}