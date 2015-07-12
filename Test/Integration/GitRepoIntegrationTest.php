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

    /**
     * @var string
     */
    private $repo_name = 'TestRepo';

    /**
     * @var array
     */
    private $branches = ['feature/new-stuff', 'bug/bad-bug'];

    /**
     * @var array
     */
    private $tags = ['v0.1.0', 'v.1.3.4'];

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
        mkdir($this->url, 0777, true);

        chdir($this->url);

        touch('one.txt');
        touch('two.txt');

        exec("git init .");
        exec("git add .");
        exec("git commit -m 'first commit'");

        foreach ($this->branches as $branch) {
            exec("git checkout -b $branch");
        }

        foreach($this->tags as $index => $tag){
            exec("git tag -a $tag -m 'tag number $index'");
        }
    }
}