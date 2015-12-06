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
    public function add($url)
    {
        $this->data []= $url;
    }

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

    /**
     * @param $host
     * @param $token
     */
    public function addToken($host, $token)
    {

    }

    /**
     * @param $host
     * @return string
     */
    public function getToken($host)
    {

    }
}
