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
     * @return array
     */
    public function getRepositoryNames()
    {
        return [
        ];
    }
}