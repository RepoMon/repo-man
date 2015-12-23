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
     * @param $url
     * @param $owner
     * @param $language
     * @param $dependency_manager
     * @return bool
     */
    public function add($url, $owner, $language, $dependency_manager)
    {
        $this->data []= ['url' => $url, 'owner' => $owner, 'lang' => $language, 'dependency_manager' => $dependency_manager];
        return true;
    }

    /**
     * @param $url
     * @return array
     * @throws UnavailableException
     */
    public function get($url)
    {
        if (in_array($url, $this->data)){
            return $this->data[$url];
        } else {
            throw new UnavailableException;
        }
    }

    /**
     * @param $owner
     * @return array
     */
    public function getAll($owner)
    {
        $repositories = [];

        foreach($this->data as $repository) {
            if ($repository['owner'] === $owner) {
                $repositories [] = $repository;
            }
        }
        return $repositories;
    }

    /**
     * @param $url
     * @return bool
     */
    public function delete($url)
    {
        unset($this->data[$url]);
        return true;
    }
}
