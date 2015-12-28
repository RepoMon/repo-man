<?php namespace Ace\RepoMan\Command;

use Ace\RepoMan\Domain\CommandLine;
use Ace\RepoMan\Domain\Repository;
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
     * @var string
     */
    private $repository_dir;

    /**
     * @param StoreInterface $store
     * @param $repository_dir
     */
    public function __construct(StoreInterface $store, $repository_dir)
    {
        $this->store = $store;
        $this->repository_dir = $repository_dir;
    }

    /**
     * @param $type
     * @param $repository_url
     * @param $token
     * @return CurrentUpdater|VersionUpdater
     */
    public function create($type, $repository_url, $token)
    {
        // use the data associated with the repository url to construct the command instance
        $repository_data = $this->store->get($repository_url);

        // create a repository instance from the data returned by store
        $repository = new Repository(
            $repository_data['url'],
            $this->repository_dir,
            $token
        );

        switch ($type) {

            case 'dependencies/update/required':
                return new VersionUpdater($repository);

            case 'dependencies/update/current':
                return new CurrentUpdater($repository);
        }
    }
}
