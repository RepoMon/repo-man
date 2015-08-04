<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Domain\Repository;

/**
 * Update the dependencies of a repository
 *
 *  Branches from latest tag
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
        $this->repository->update();

        // generate branch name from current tag name
        $latest_tag = $this->repository->getLatestTag();
        $branch = 'feature/update-' . $latest_tag;

        // create a new branch if one is not present locally
        if (!$this->repository->isLocalBranch($branch)) {
            $this->repository->branch($branch, $latest_tag);
        }

        $this->repository->checkout($branch);

        $this->repository->getDependencySet()->setRequiredVersions($data['require']);

        // run git commit
        $this->repository->commit('Updates dependencies');

        // run git push origin $branch
        $this->repository->push($branch);

        return true;
    }
}