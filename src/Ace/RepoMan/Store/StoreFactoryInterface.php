<?php namespace Ace\RepoMan\Store;
/**
 * @author timrodger
 * Date: 12/12/15
 */
interface StoreFactoryInterface
{

    /**
     * @return \Ace\RepoMan\Store\StoreInterface
     */
    public function create();
}