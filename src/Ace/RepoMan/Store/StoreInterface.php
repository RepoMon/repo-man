<?php namespace Ace\RepoMan\Store;

/**
 * @author timrodger
 * Date: 18/07/15
 */
interface StoreInterface
{
    public function getAll($owner);

    public function get($full_name);

    public function add($url, $full_name, $owner, $description, $lang, $dependency_manager, $timezone, $active, $is_private);

    public function activate($full_name);

    public function deactivate($full_name);

    public function delete($full_name);

}
