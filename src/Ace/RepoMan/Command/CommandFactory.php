<?php namespace Ace\RepoMan\Command;

use Ace\RepoMan\Domain\CommandLine;
use Ace\RepoMan\Store\StoreInterface;

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
     * @return \Ace\RepoMan\Command\CommandInterface
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
