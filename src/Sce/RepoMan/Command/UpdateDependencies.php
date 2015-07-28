<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Domain\Composer;
use Sce\RepoMan\Domain\Repository;

/**
 * Update the dependencies of a repository
 *
 *  Branches from master (always?)
 *  Installs the updates
 *  Commits changes
 *  Pushes new branch to origin
 */
class UpdateDependencies implements CommandInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Composer
     */
    private $composer;

    public function __construct(Repository $repository, Composer $composer)
    {
        $this->repository = $repository;
        $this->composer = $composer;
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

        $this->composer->setRequiredVersions($data['require']);

        // Add composer.json and composer.lock to git branch
        $this->repository->add('composer.json');
        $this->repository->add('composer.lock');

        // run git commit
        $this->repository->commit('Updates composer dependencies');

        // run git push origin $branch
        $this->repository->push($branch);

        return true;
    }
}