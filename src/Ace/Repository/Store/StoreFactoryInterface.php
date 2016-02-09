<?php namespace Ace\Repository\Store;
/**
 * @author timrodger
 * Date: 12/12/15
 */
interface StoreFactoryInterface
{

    /**
     * @return \Ace\Repository\Store\StoreInterface
     */
    public function create();
}
