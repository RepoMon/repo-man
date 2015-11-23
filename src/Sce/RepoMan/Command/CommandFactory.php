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
     * @return \Sce\RepoMan\Command\CommandInterface
     */
    public function create($type, $repository_url)
    {
        $repository = $this->store->get($repository_url);

        switch ($type) {

            case 'dependencies/update/required':
                return new VersionUpdater($repository);

            case 'dependencies/update/current':
                return new CurrentUpdater($repository);
        }
    }
}