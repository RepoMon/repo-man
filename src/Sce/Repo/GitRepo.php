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
     *
     * runs git remote update and git fetch --tags
     */
    public function update()
    {

    }

    public function branch($name)
    {

    }

    public function tag($name)
    {

    }

    public function add($name)
    {

    }

    public function commit()
    {

    }

    public function push()
    {

    }

    /**
     * return a list of branch names for the local repo
     * @return array
     */
    public function listBranches()
    {

    }

    /**
     * Get the list of tags for the local repo
     * @return array
     */
    public function listTags()
    {

    }

    /**
     * Checkout the branch or tag with this name
     * @param $name
     */
    public function checkout($name)
    {

    }

    /**
     * @param $name
     * @return string the file named $name in checkout
     */
    public function getFile($name)
    {

    }
}