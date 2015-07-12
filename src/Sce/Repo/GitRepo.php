<?php namespace Sce\Repo;

/**
 * Class GitRepo
 * @package Sce\Repo
 * Represents a git repo
 */
class GitRepo
{
    /**
     * @var string url of remote git repo
     */
    private $url;

    /**
     * @var string location to clone remote repo into
     */
    private $directory;

    /**
     * @var name of checkout
     */
    private $name;

    /**
     * @param $url
     * @param $directory
     */
    public function __construct($url, $directory)
    {
        $this->url = $url;
        $this->directory = $directory;
        $parts = explode('/', $this->url);
        $this->name = array_pop($parts);
    }

    /**
     * Update the local repo from the remote
     * clones repo if it has not been checked out out yet
     * runs git remote update and git fetch --tags
     */
    public function update()
    {
        // cd to dir
        chdir($this->directory);

        // check if local repo exists
        if (!is_dir($this->directory . '/' . $this->name)) {
            exec('git clone ' . $this->url);
        }

        chdir($this->directory . '/' . $this->name);
        exec('git remote update');
        exec('git fetch --tags origin');
    }

    /**
     * return a list of branch names for the local repo
     * @return array
     */
    public function listLocalBranches()
    {
        chdir($this->directory . '/' . $this->name);
        exec('git branch', $output);

        var_dump($output);

        return $output;
    }

    /**
     * Get the list of tags for the local repo
     * @return array
     */
    public function listTags()
    {

    }

    /**
     * @param $name
     * @return string the file named $name in checkout
     */
    public function getFile($name)
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
     * Checkout the branch or tag with this name
     * @param $name
     */
    public function checkout($name)
    {

    }
}