<?php namespace Sce\RepoMan\Command;

use Sce\RepoMan\Domain\CommandLine;
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

    /**
     * @var CommandLine
     */
    private $command_line;

    public function __construct(Repository $repository, CommandLine $command_line)
    {
        $this->repository = $repository;
        $this->command_line = $command_line;
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

        if (!$this->repository->hasFile('composer.json')){
            return false;
        }

        // create a composer object from the files in repository
        $composer_json = json_decode($this->repository->getFile('composer.json'), 1);

        if (!is_array($composer_json)){
            return false;
        }

        $composer = new Composer($composer_json, []);

        foreach($data['require'] as $library => $version) {
            $composer->setRequireVersion($library, $version);
        }

        // write the new composer config back to the file
        $this->repository->setFile('composer.json', json_encode($composer->getComposerJson()));

        $this->repository->removeFile('composer.lock');

        // run composer install
        $this->command_line->exec('composer install');

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