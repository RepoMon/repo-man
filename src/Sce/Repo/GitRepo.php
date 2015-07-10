<?php namespace Sce\Repo;

/**
 * Class GitRepo
 * @package Sce\Repo
 * Represents a git repo
 */
class GitRepo
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $directory;

    public function __construct($url, $directory)
    {
        $this->url = $url;
        $this->directory = $directory;
    }

    /**
     * Update the local repo from the remote
     */
    public function update()
    {

    }

    public function listBranches()
    {

    }

    public function listTags()
    {

    }

    public function checkout($name)
    {

    }
}