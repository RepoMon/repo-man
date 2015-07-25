<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Domain\Repository;

/**
 * Update the dependencies of a composer configuration
 *
 *  Branches from master (always?)
 *  Installs the updates
 *  Commits changes
 *  Pushes new branch to origin
 */
class UpdateComposerDependencies implements CommandInterface
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $data
     */
    public function execute($data)
    {

    }
}