<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Store\StoreInterface;

/**
 * @author timrodger
 * Date: 26/07/15
 */
class CommandFactory
{

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * @param $repository_url string
     */
    public function create($type, $repository_url)
    {

    }
}