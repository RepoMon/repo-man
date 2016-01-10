<?php namespace Ace\RepoMan\Command;

use Ace\RepoMan\Domain\Repository;

/**
 * @author timrodger
 * Date: 26/07/15
 */
class CommandFactory
{

    /**
     * @var string
     */
    private $repository_dir;

    /**
     * @param $repository_dir
     */
    public function __construct($repository_dir)
    {
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
        $repository = new Repository(
            $repository_url,
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
