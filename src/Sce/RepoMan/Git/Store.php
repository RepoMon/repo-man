<?php namespace Sce\RepoMan\Git;

use Sce\RepoMan\Configuration;

/**
 * @author timrodger
 * Date: 17/07/15
 */
class Store
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
    public function getAll()
    {
        if (count($this->repositories)){
            return $this->repositories;
        }

        // get the repos from redis

        return $this->repositories;
    }
}