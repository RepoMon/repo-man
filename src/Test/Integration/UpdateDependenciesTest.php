<?php

use Sce\RepoMan\Domain\Repository as GitRepo;
use Sce\RepoMan\Command\UpdateDependencies;
use Sce\RepoMan\Domain\CommandLine;
use Sce\RepoMan\Command\CommandInterface;
use Sce\RepoMan\Domain\DependencySet;

/**
 * @group integration
 * @group filesystem
 * @author timrodger
 * Date: 27/07/15
 */
class UpdateDependenciesTest extends PHPUnit_Framework_TestCase
{
    /**
     * name of file in repo
     */
    const FILE_ONE = 'composer.json';

    /**
     * Other file name
     */
    const FILE_TWO = 'two.txt';

    /**
     * @var string
     */
    private $url;

    /**
     * @var
     */
    private $directory;

    /**
     * @var string
     */
    private $repo_name = 'TestRepo';

    /**
     * @var array
     */
    private $tags = ['v0.0.1'];

    /**
     * @var GitRepo
     */
    private $git_repo;

    /**
     * @var CommandInterface
     */
    private $command;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->directory = TEMP_DIR;
        $this->url = $this->directory . '/Fixtures/' . $this->repo_name;

        if (!is_dir($this->directory)) {
            mkdir($this->directory);
        }

        $this->createGitRepo();
    }

    public function tearDown()
    {
        // don't use broken built in rmdir function
        exec("rm -rf " . $this->directory);

        parent::tearDown();
    }

    protected function givenACheckout()
    {
        $this->git_repo = new GitRepo($this->url, $this->directory);
    }

    protected function givenACommand()
    {
        $command_line = new CommandLine($this->git_repo->getCheckoutDirectory());
        $composer = new DependencySet($this->git_repo, $command_line);

        $this->command = new UpdateDependencies(
            $this->git_repo,
            $composer
        );
    }

    public function testUpdateDependencies()
    {
        $this->givenACheckout();
        $this->givenACommand();
        $data = ['require' => ['symfony/symfony' => '2.7.2']];

        $result = $this->command->execute($data);
        $this->assertTrue($result);
    }

    private function createGitRepo()
    {
        if (!is_dir($this->url)) {
            mkdir($this->url, 0777, true);
        }

        chdir($this->url);

        file_put_contents(self::FILE_ONE, json_encode(['require' => ['behat/behat' => '2.5.3']]));
        file_put_contents(self::FILE_TWO, 'two contents');

        exec("git init .");
        exec("git add .");
        exec("git commit -m 'first commit'", $output);

        foreach($this->tags as $index => $tag){
            exec("git tag -a $tag -m 'tag number $index'");
        }
    }
}