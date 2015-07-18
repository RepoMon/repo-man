<?php namespace Sce\RepoMan\Git;

/**
 * @author timrodger
 * Date: 18/07/15
 */
interface StoreInterface
{
    public function getAll();

    public function add($url);
}