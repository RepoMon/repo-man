<?php

use Sce\Repo\GitRepo;

/**
 * @author timrodger
 * Date: 12/07/15
 */
class GitRepoIntegrationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var
     */
    private $directory;

    public function setUp()
    {
        parent::setUp();

        $this->directory = TEMP_DIR;

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

    public function testUpdate()
    {
        $git_repo = new GitRepo($this->url, $this->directory);

        $git_repo->update();

        $parts = explode('/', $this->url);
        $name = array_pop($parts);

        $this->assertTrue(is_dir($this->directory . '/'. $name));
    }

    private function createGitRepo()
    {
        $name = 'TestRepo';
        $branch = 'feature/new-stuff';
        $tag = 'v0.1.0';

        mkdir($this->directory . "/Fixtures");
        mkdir($this->directory . "/Fixtures/" . $name);

        chdir($this->directory . "/Fixtures/" . $name);

        touch('one.txt');
        touch('two.txt');

        exec("git init .");
        exec("git add .");
        exec("git commit -m 'first commit'");

        exec("git checkout -b $branch");
        exec("git tag -a $tag -m 'first tag'");

        $this->url = $this->directory . '/Fixtures/' . $name;
    }
}