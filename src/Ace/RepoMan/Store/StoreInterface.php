<?php namespace Ace\RepoMan\Store;

/**
 * @author timrodger
 * Date: 18/07/15
 */
interface StoreInterface
{
    public function getAll($owner);

    public function get($url);

    public function add($url, $owner, $description, $lang, $dependency_manager, $timezone, $active);

    public function delete($url);

}
