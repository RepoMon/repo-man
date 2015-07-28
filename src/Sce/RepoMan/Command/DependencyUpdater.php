<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Domain\DependencySet;
use Sce\RepoMan\Domain\Repository;

/**
 * Update the dependencies of a repository
 *
 *  Branches from master (always?)
 *  Installs the updates
 *  Commits changes
 *  Pushes new branch to origin
 */
class DependencyUpdater implements CommandInterface
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
        if (!$this->repository->update()) {
            return false;
        }

        // generate branch name from current tag name
        $latest_tag = $this->repository->getLatestTag();
        $branch = 'feature/update-' . $latest_tag;

        $this->repository->branch($branch, $latest_tag);

        $this->repository->checkout($branch);

        $this->repository->getDependencySet()->setRequiredVersions($data['require']);

        // run git commit
        $this->repository->commit('Updates composer dependencies');

        // run git push origin $branch
        $this->repository->push($branch);

        return true;
    }
}