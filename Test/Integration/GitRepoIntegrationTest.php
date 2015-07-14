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

    public function testListLocalBranches()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $local_branches = $git_repo->listLocalBranches();

        $this->assertSame(1, count($local_branches));
        $this->assertSame(['master'], $local_branches);
    }

    public function testIsLocalBranch()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $result = $git_repo->isLocalBranch('master');
        $this->assertTrue($result);

        $result = $git_repo->isLocalBranch('not-a-branch');
        $this->assertFalse($result);

        $result = $git_repo->isLocalBranch('feature/new-stuff');
        $this->assertFalse($result);
    }

    public function testListTags()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $tags = $git_repo->listTags();

        $this->assertSame(count($this->tags), count($tags));

        foreach($this->tags as $tag){
            $this->assertTrue(in_array($tag, $tags));
        }
    }

    public function testListAllBranches()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $branches = $git_repo->listAllBranches();

        $this->assertSame(3, count($branches));

        $this->assertTrue(in_array('master', $branches));

        foreach($this->branches as $branch){
            $this->assertTrue(in_array($branch, $branches));
        }
    }

    public function testGetFile()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $contents = $git_repo->getFile('one.txt');

        $this->assertSame('one contents', $contents);
    }

    private function createGitRepo()
    {
        mkdir($this->url, 0777, true);

        chdir($this->url);

        file_put_contents('one.txt', 'one contents');
        file_put_contents('two.txt', 'two contents');

        exec("git init .");
        exec("git add .");
        exec("git commit -m 'first commit'", $output);

        foreach ($this->branches as $branch) {
            exec("git checkout -b $branch",  $output);
        }

        // then checkout master
        exec("git checkout master", $output);

        foreach($this->tags as $index => $tag){
            exec("git tag -a $tag -m 'tag number $index'");
        }
    }
}