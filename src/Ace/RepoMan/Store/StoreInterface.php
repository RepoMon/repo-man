<?php namespace Ace\RepoMan\Store;

/**
 * @author timrodger
 * Date: 18/07/15
 */
interface StoreInterface
{
    public function getAll($owner);

    public function get($url);

    public function add($url, $owner, $language, $dependency_manager);

    public function delete($url);

}
