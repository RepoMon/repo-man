<?php namespace Sce\RepoMan\Git;

/**
 * Class GitRepo
 * @package Sce\Repo
 * Represents a git repo
 */
class Repository
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
     * @var string name of checkout
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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
            exec('git clone ' . $this->url, $output);
        }

        $this->execGitCommand('git remote update');
        $this->execGitCommand('git fetch --tags origin');
    }

    /**
     * return a list of branch names for the local repo
     *
     * @return array
     */
    public function listLocalBranches()
    {
        $branches = $this->execGitCommand('git branch');

        return array_map(function($name){
            return trim($name, '* ');
        }, $branches);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isLocalBranch($name)
    {
        return in_array($name, $this->listLocalBranches());
    }

    /**
     * @return array
     */
    public function listAllBranches()
    {

        $branches = $this->execGitCommand('git branch -a');

        $branches = array_map(function($name){
            return trim($name, '* ');
        }, $branches);

        $branches = array_map(function($name) {
            return preg_replace('/^remotes\/origin\//', '', $name);
        }, $branches);

        // de-duplicate array and remove HEAD
        $branches = array_filter($branches, function($name){
            return !preg_match('/^HEAD/', $name);
        });

        return array_unique($branches);
    }

    /**
     * Get the list of tags for the local repo
     * @return array
     */
    public function listTags()
    {
        return $this->execGitCommand('git tag -l');
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
     * @return bool
     */
    public function hasFile($name)
    {
        return file_exists($this->directory . '/' . $this->name . '/' . $name);
    }

    /**
     * @param $name
     * @return string the contents of file named $name in checkout
     */
    public function getFile($name)
    {
        if ($this->hasFile($name)) {
            return file_get_contents($this->directory . '/' . $this->name . '/' . $name);
        }
    }

    /**
     * Create a new branch
     * @param $name
     */
    public function branch($name)
    {

    }

    /**
     * Create a new tag
     * @param $name string
     * @param $comment string
     */
    public function tag($name, $comment)
    {

    }

    /**
     * Add a file
     * @param $name
     */
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
     * @param $cmd string
     * @return array
     */
    private function execGitCommand($cmd)
    {
        chdir($this->directory . '/' . $this->name);
        exec($cmd, $output);
        return $output;
    }

}