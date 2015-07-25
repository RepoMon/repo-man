<?php

use Sce\RepoMan\Domain\Repository as GitRepo;

/**
 * @group integration
 * @group filesystem
 *
 * @author timrodger
 * Date: 12/07/15
 */
class GitRepoIntegrationTest extends PHPUnit_Framework_TestCase
{

    /**
     * name of file in repo
     */
    const FILE_ONE = 'one.txt';

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

    public function testGetLatestTag()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $latest_tag = $git_repo->getLatestTag();
        $this->assertSame('v.1.3.4', (string) $latest_tag);
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

        $contents = $git_repo->getFile(self::FILE_ONE);

        $this->assertSame('one contents', $contents);
    }

    public function testGetFileReturnsNullForMissingFile()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $contents = $git_repo->getFile('not-there');

        $this->assertSame(null, $contents);
    }

    public function testSetFileOverwritesExisting()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $new_contents = 'new contents';
        $git_repo->setFile(self::FILE_ONE, $new_contents);

        $actual = $git_repo->getFile(self::FILE_ONE);

        $this->assertSame($new_contents, $actual);
    }

    public function testSetFileCreatesNewFile()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $new_contents = 'new contents';
        $new_file = 'a-new-file.txt';
        $this->assertFalse($git_repo->hasFile($new_file));

        $git_repo->setFile($new_file, $new_contents);

        $this->assertTrue($git_repo->hasFile($new_file));

        $actual = $git_repo->getFile($new_file);
        $this->assertSame($new_contents, $actual);
    }

    public function testHasFile()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $result = $git_repo->hasFile(self::FILE_ONE);

        $this->assertTrue($result);
    }

    public function testHasFileReturnsFalseForMissingFile()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $result = $git_repo->hasFile('not-there');

        $this->assertFalse($result);
    }

    public function testRemoveFile()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $exists = $git_repo->hasFile(self::FILE_ONE);
        $this->assertTrue($exists);

        $git_repo->removeFile(self::FILE_ONE);

        $exists = $git_repo->hasFile(self::FILE_ONE);
        $this->assertFalse($exists);
    }

    public function testRemoveFileWorksIfFileDoesNotExist()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $exists = $git_repo->hasFile('four.txt');
        $this->assertFalse($exists);

        $git_repo->removeFile('four.txt');

        $exists = $git_repo->hasFile('four.txt');
        $this->assertFalse($exists);
    }

    public function testGetUrl()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $result = $git_repo->getUrl();
        $this->assertSame($this->url, $result);
    }

    public function testGetId()
    {
        $expected = base64_encode($this->url);

        $git_repo = new GitRepo($this->url, $this->directory);
        $result = $git_repo->getId();

        $this->assertSame($expected, $result);
    }

    public function testBranch()
    {
        $name = 'feature/special-sauce';
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $result = $git_repo->isLocalBranch($name);
        $this->assertFalse($result);

        $git_repo->branch($name);

        $result = $git_repo->isLocalBranch($name);
        $this->assertTrue($result);
    }

    public function testCheckout()
    {
        $name = 'feature/special-sauce';
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $git_repo->branch($name);
        $git_repo->checkout($name);

    }

    public function testAddFile()
    {
        $git_repo = new GitRepo($this->url, $this->directory);
        $git_repo->update();

        $status = $git_repo->status();
        $this->assertSame([], $status);

        $new_contents = 'new contents';
        $git_repo->setFile(self::FILE_ONE, $new_contents);

        $status = $git_repo->status();

        $this->assertSame(' M one.txt', $status[0]);

        $git_repo->add(self::FILE_ONE);

        $status = $git_repo->status();

        $this->assertSame('M  one.txt', $status[0]);
    }

    private function createGitRepo()
    {
        if (!is_dir($this->url)) {
            mkdir($this->url, 0777, true);
        }

        chdir($this->url);

        file_put_contents(self::FILE_ONE, 'one contents');
        file_put_contents(self::FILE_TWO, 'two contents');

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