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
     * @param $full_name
     * @param $owner
     * @param $description
     * @param $lang
     * @param $dependency_manager
     * @param $timezone
     * @param $active
     * @return bool
     */
    public function add($url, $full_name, $owner, $description, $lang, $dependency_manager, $timezone, $active)
    {
        $this->data[$full_name] = [
            'url' => $url,
            'full_name' => $full_name,
            'owner' => $owner,
            'description' => $description,
            'lang' => $lang,
            'dependency_manager' => $dependency_manager,
            'timezone' => $timezone,
            'active' => $active
        ];
        return true;
    }

    /**
     * @param $url
     * @return array
     * @throws UnavailableException
     */
    public function get($full_name)
    {
        if (in_array($full_name, $this->data)){
            return $this->data[$full_name];
        } else {
            throw new NotFoundException;
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
     * @param $full_name
     * @throws UnavailableException
     */
    public function activate($full_name)
    {
        $repo = $this->get($full_name);
        $repo['active'] = true;
        $this->data[$full_name] = $repo;
    }

    /**
     * @param $full_name
     * @throws UnavailableException
     */
    public function deactivate($full_name)
    {
        $repo = $this->get($full_name);
        $repo['active'] = false;
        $this->data[$full_name] = $repo;
    }

    /**
     * @param $url
     * @return bool
     */
    public function delete($full_name)
    {
        unset($this->data[$full_name]);
        return true;
    }
}
