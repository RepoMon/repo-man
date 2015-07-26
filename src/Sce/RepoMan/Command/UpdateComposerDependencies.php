<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Domain\Composer;
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
        $success = $this->repository->update();
        if (!$success) {
            return false;
        }

        // generate branch name from current tag name
        $latest_tag = $this->repository->getLatestTag();
        $branch = 'feature/update-' . $latest_tag;

        $this->repository->branch($branch, $latest_tag);

        $this->repository->checkout($branch);

        if (!$this->repository->hasFile('composer.json')){
            return false;
        }

        // create a composer object from the files in repository
        $composer_json = json_decode($this->repository->getFile('composer.json'), 1);

        if (!is_array($composer_json)){
            return false;
        }

        // if lock is not present use an empty array
        $composer_lock = json_decode($this->repository->getFile('composer.lock'), 1);

        if (!is_array($composer_lock)){
            $composer_lock = [];
        }

        $composer = new Composer($composer_json, $composer_lock);

    }
}