<?php namespace Sce\RepoMan\Domain;

use Sce\RepoMan\Configuration;

/**
 * @author timrodger
 * Date: 17/07/15
 */
class GitRepoCollection
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var array of GitRepo instances
     */
    private $git_repos = [];

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
        $repositories = [];

        foreach($this->config->getRepositoryNames() as $name){
            $repositories []= new GitRepo($name, $this->config->getRepoDir());
        }
        return $repositories;
    }
}