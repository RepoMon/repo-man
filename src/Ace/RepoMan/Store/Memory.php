<?php namespace Ace\RepoMan\Store;

use Ace\RepoMan\Configuration;
use Ace\RepoMan\Domain\Repository;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class Memory implements StoreInterface
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
    public function add($url, $owner, $language, $dependency_manager)
    {
        $this->data []= $url;
    }

    /**
     * @param $url
     * @return Repository
     * @throws UnavailableException
     */
    public function get($url)
    {
        if (in_array($url, $this->data)){
            return new Repository($url, $this->config->getRepoDir());
        } else {
            throw new UnavailableException;
        }
    }

    /**
     * Return the template contents for $path
     * @param string $owner
     */
    public function getAll($owner)
    {
        $repositories = [];

        foreach($this->data as $url) {
            $repositories []= new Repository($url, $this->config->getRepoDir());
        }

        return $repositories;
    }

    /**
     * @param string $url
     */
    public function delete($url)
    {

    }
}
