<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Domain\CommandLine;
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
        switch ($type) {

            case "dependencies/update":
                $repository = $this->store->get($repository_url);
                return new UpdateDependencies($repository);
        }
    }
}