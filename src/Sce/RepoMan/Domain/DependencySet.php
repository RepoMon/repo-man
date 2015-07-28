<?php namespace Sce\RepoMan\Domain;

use Sce\RepoMan\Domain\CommandLine;
use Sce\RepoMan\Domain\ComposerConfig;
use Sce\RepoMan\Domain\Repository;
use Exception;
use Sce\RepoMan\Exception\FileNotFoundException;
use Sce\RepoMan\Exception\InvalidFileContentsException;

/**
 * @todo rename ComposerDependencySet
 * @package Sce\RepoMan\Domain
 */
class DependencySet implements DependencySetInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var CommandLine
     */
    private $command_line;

    /**
     * @param Repository  $repository
     * @param CommandLine $command_line
     */
    public function __construct(Repository $repository, CommandLine $command_line)
    {
        $this->repository = $repository;
        $this->command_line = $command_line;
    }

    /**
     * Update the composer config for the repository to use the parameter versions
     *
     * @param array $versions
     */
    public function setRequiredVersions(array $versions)
    {
        if (!$this->repository->hasFile('composer.json')){
            throw new FileNotFoundException("'composer.json not found'");
        }

        // create a composer object from the files in repository
        $composer_json = json_decode($this->repository->getFile('composer.json'), 1);

        if (!is_array($composer_json)){
            throw new InvalidFileContentsException("'composer.json' is invalid");
        }

        $composer = new ComposerConfig($composer_json, []);

        foreach($versions as $library => $version) {
            $composer->setRequireVersion($library, $version);
        }

        // write the new composer config back to the file
        $this->repository->setFile(
            'composer.json',
            json_encode($composer->getComposerJson(), JSON_PRETTY_PRINT)
        );

        $this->repository->removeFile('composer.lock');

        // run composer install
        $this->command_line->exec('composer install  --prefer-dist --no-scripts');

        // Add composer.json and composer.lock to git branch
        $this->repository->add('composer.json');
        $this->repository->add('composer.lock');
    }
}