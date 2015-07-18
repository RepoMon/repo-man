<?php namespace Sce\RepoMan\Git;

use Sce\RepoMan\Configuration;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class MemoryStore implements StoreInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @param $url
     */
    public function add($url)
    {
        $this->data []= $url;
    }

    /**
     * Return the template contents for $path
     * @param $path
     */
    public function getAll()
    {
        $repositories = [];

        foreach($this->data as $url) {
            $repositories []= new Repository($url, $this->config->getRepoDir());
        }

        return $repositories;
    }

}