<?php namespace Sce\RepoMan\Store;

/**
 * @author timrodger
 * Date: 18/07/15
 */
interface StoreInterface
{
    public function getAll();

    public function get($url);

    public function add($url);

    public function addToken($host, $token);
}