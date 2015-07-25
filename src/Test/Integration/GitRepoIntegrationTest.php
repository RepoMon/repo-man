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

    /**
     * @var GitRepo
     */
    private $git_repo;

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
        $this->git_repo->update();
    }

    public function testUpdate()
    {
        $this->givenACheckout();

        $parts = explode('/', $this->url);
        $name = array_pop($parts);

        $this->assertTrue(is_dir($this->directory . '/'. $name));
    }

    public function testListLocalBranches()
    {
        $this->givenACheckout();

        $local_branches = $this->git_repo->listLocalBranches();

        $this->assertSame(1, count($local_branches));
        $this->assertSame(['master'], $local_branches);
    }

    public function testIsLocalBranch()
    {
        $this->givenACheckout();

        $result = $this->git_repo->isLocalBranch('master');
        $this->assertTrue($result);

        $result = $this->git_repo->isLocalBranch('not-a-branch');
        $this->assertFalse($result);

        $result = $this->git_repo->isLocalBranch('feature/new-stuff');
        $this->assertFalse($result);
    }

    public function testListTags()
    {
        $this->givenACheckout();

        $tags = $this->git_repo->listTags();

        $this->assertSame(count($this->tags), count($tags));

        foreach($this->tags as $tag){
            $this->assertTrue(in_array($tag, $tags));
        }
    }

    public function testGetLatestTag()
    {
        $this->givenACheckout();

        $latest_tag = $this->git_repo->getLatestTag();
        $this->assertSame('v.1.3.4', (string) $latest_tag);
    }

    public function testListAllBranches()
    {
        $this->givenACheckout();

        $branches = $this->git_repo->listAllBranches();

        $this->assertSame(3, count($branches));

        $this->assertTrue(in_array('master', $branches));

        foreach($this->branches as $branch){
            $this->assertTrue(in_array($branch, $branches));
        }
    }

    public function testGetFile()
    {
        $this->givenACheckout();

        $contents = $this->git_repo->getFile(self::FILE_ONE);

        $this->assertSame('one contents', $contents);
    }

    public function testGetFileReturnsNullForMissingFile()
    {
        $this->givenACheckout();

        $contents = $this->git_repo->getFile('not-there');

        $this->assertSame(null, $contents);
    }

    public function testSetFileOverwritesExisting()
    {
        $this->givenACheckout();

        $new_contents = 'new contents';
        $this->git_repo->setFile(self::FILE_ONE, $new_contents);

        $actual = $this->git_repo->getFile(self::FILE_ONE);

        $this->assertSame($new_contents, $actual);
    }

    public function testSetFileCreatesNewFile()
    {
        $this->givenACheckout();

        $new_contents = 'new contents';
        $new_file = 'a-new-file.txt';
        $this->assertFileDoesNotExistInRepo($new_file);

        $this->git_repo->setFile($new_file, $new_contents);

        $this->assertFileExistsInRepo($new_file);

        $actual = $this->git_repo->getFile($new_file);
        $this->assertSame($new_contents, $actual);
    }

    public function testHasFile()
    {
        $this->givenACheckout();

        $result = $this->git_repo->hasFile(self::FILE_ONE);

        $this->assertTrue($result);
    }

    public function testHasFileReturnsFalseForMissingFile()
    {
        $this->givenACheckout();

        $result = $this->git_repo->hasFile('not-there');

        $this->assertFalse($result);
    }

    public function testRemoveFile()
    {
        $this->givenACheckout();

        $this->assertFileExistsInRepo(self::FILE_ONE);

        $this->git_repo->removeFile(self::FILE_ONE);

        $this->assertFileDoesNotExistInRepo(self::FILE_ONE);
    }

    public function testRemoveFileWorksIfFileDoesNotExist()
    {
        $this->givenACheckout();

        $this->assertFileDoesNotExistInRepo('four.txt');

        $this->git_repo->removeFile('four.txt');

        $this->assertFileDoesNotExistInRepo('four.txt');
    }

    public function testGetUrl()
    {
        $this->git_repo = new GitRepo($this->url, $this->directory);

        $result = $this->git_repo->getUrl();
        $this->assertSame($this->url, $result);
    }

    public function testGetId()
    {
        $expected = base64_encode($this->url);

        $this->git_repo = new GitRepo($this->url, $this->directory);

        $result = $this->git_repo->getId();

        $this->assertSame($expected, $result);
    }

    public function testBranch()
    {
        $name = 'feature/special-sauce';
        $this->givenACheckout();

        $result = $this->git_repo->isLocalBranch($name);
        $this->assertFalse($result);

        $this->git_repo->branch($name);

        $result = $this->git_repo->isLocalBranch($name);
        $this->assertTrue($result);
    }

    public function testCheckout()
    {
        $name = 'feature/special-sauce';
        $this->givenACheckout();

        $this->git_repo->branch($name);
        $this->git_repo->checkout($name);
    }

    public function testAddFile()
    {
        $this->givenACheckout();

        $this->assertNoChanges();

        $new_contents = 'new contents';
        $this->git_repo->setFile(self::FILE_ONE, $new_contents);

        $this->assertFileModified(self::FILE_ONE, 0);

        $this->git_repo->add(self::FILE_ONE);

        $this->assertFileAdded(self::FILE_ONE, 0);
    }

    /**
     * @expectedException \Sce\RepoMan\Domain\ExecutionException
     */
    public function testCommitOnUnchangedRepoThrowsException()
    {
        $this->givenACheckout();

        $this->assertNoChanges();

        $this->git_repo->commit('No changes');

        $this->assertNoChanges();
    }

    public function testCommit()
    {
        $this->givenACheckout();
        $new_contents = 'new contents';
        $this->git_repo->setFile(self::FILE_ONE, $new_contents);
        $this->git_repo->add(self::FILE_ONE);
        $this->assertFileAdded(self::FILE_ONE, 0);

        $this->git_repo->commit('Updates x');

        $this->assertNoChanges();
    }

    /**
     * If push fails The Repository will throw an exception
     * This test asserts that the push command is successful as it does not throw an exception
     */
    public function testPush()
    {
        $name = 'feature/cool-beans';
        $this->givenACheckout();
        $this->git_repo->branch($name);
        $this->git_repo->checkout($name);
        $new_contents = 'new contents';

        $this->git_repo->setFile(self::FILE_ONE, $new_contents);
        $this->git_repo->add(self::FILE_ONE);
        $this->git_repo->commit('Updates x');

        $this->git_repo->push();
    }


    private function assertNoChanges()
    {
        $status = $this->git_repo->status();
        $this->assertSame([], $status);
    }

    protected function assertFileModified($name, $index)
    {
        $status = $this->git_repo->status();
        $this->assertSame(' M ' . $name, $status[$index]);
    }

    protected function assertFileAdded($name, $index)
    {
        $status = $this->git_repo->status();
        $this->assertSame('M  ' . $name, $status[$index]);
    }

    protected function assertFileExistsInRepo($name)
    {
        $exists = $this->git_repo->hasFile($name);
        $this->assertTrue($exists);
    }

    protected function assertFileDoesNotExistInRepo($name)
    {
        $exists = $this->git_repo->hasFile($name);
        $this->assertFalse($exists);
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