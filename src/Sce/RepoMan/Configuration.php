<?php namespace Sce\RepoMan;

/*
 * @author tim rodger
 * Date: 29/03/15
 */
class Configuration
{
    /**
     * @var string
     */
    private $repo_dir;

    /**
     * @param $base_dir
     */
    public function __construct($repo_dir)
    {
        $this->repo_dir = $repo_dir;
    }

    /**
     * @return string
     */
    public function getRepoDir()
    {
        return $this->repo_dir;
    }

    /**
     * @deprecated
     * @return array
     */
    public function getRepositoryNames()
    {
        return [
        ];
    }

    public function getStoreDsn()
    {
        // should contain a string like this 'tcp://172.17.0.154:6379'
        return getenv('REDIS_PORT');
    }
}