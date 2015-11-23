<?php

require_once(__DIR__.'/UpdateCommandTest.php');

use Sce\RepoMan\Command\CurrentUpdater;

/**
 * @group integration
 * @group filesystem
 * @author timrodger
 * Date: 27/07/15
 */
class CurrentUpdaterTest extends UpdateCommandTest
{

    public function setUp()
    {
        parent::setUp();

        $this->createTempDirectory();
        $this->createGitRepo();
    }

    public function tearDown()
    {
        $this->cleanUpFilesystem();

        parent::tearDown();
    }

    protected function givenACommand()
    {
        $this->command = new CurrentUpdater(
            $this->git_repo
        );
    }

    public function testUpdateCurrent()
    {
        $this->givenACheckout();
        $this->givenACommand();
        $data = [];

        // commands throw exceptions on error do not return true from execute
        $this->command->execute($data);
    }
}