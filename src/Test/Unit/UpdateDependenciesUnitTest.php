<?php

use Sce\RepoMan\Command\UpdateDependencies;
use Sce\RepoMan\Domain\FileNotFoundException;

/**
 * @group unit
 * @author timrodger
 * Date: 26/07/15
 */
class UpdateDependenciesUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Sce\RepoMan\Domain\Repository
     */
    private $mock_repository;

    /**
     * @var \Sce\RepoMan\Domain\Composer
     */
    private $mock_composer;

    public function testExecuteReturnsFalseIfUpdateFails()
    {
        $this->givenAMockRepository();
        $this->givenAMockComposer();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(false));

        $this->givenACommand();

        $data = ['require' => []];

        $result = $this->command->execute($data);
        $this->assertFalse($result);
    }

    /**
     * @expectedException Sce\RepoMan\Domain\FileNotFoundException
     */
    public function testExecuteReturnsFalseIfComposerFilesAreMissing()
    {
        $this->givenAMockRepository();
        $this->givenAMockComposer();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(true));

        $this->mock_composer->expects($this->once())
            ->method('setRequiredVersions')
            ->will($this->throwException(new \Sce\RepoMan\Domain\FileNotFoundException()));

        $this->givenACommand();

        $data = ['require' => []];

        $this->command->execute($data);
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteReturnsFalseIfComposerFilesIsNotJson()
    {
        $this->givenAMockRepository();
        $this->givenAMockComposer();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->any())
            ->method('hasFile')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->any())
            ->method('getFile')
            ->will($this->returnValue('not json'));

        $this->givenACommand();

        $data = [];

        $this->command->execute($data);
    }

    public function testExecute()
    {
        $this->givenAMockRepository();
        $this->givenAMockComposer();

        $latest_tag = 'v1.3.6';
        $new_branch = 'feature/update-' . $latest_tag;

        $this->mock_repository->expects($this->any())
            ->method('getLatestTag')
            ->will($this->returnValue($latest_tag));

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->once())
            ->method('branch')
            ->with($new_branch, $latest_tag);

        $this->mock_repository->expects($this->once())
            ->method('checkout')
            ->with($new_branch);

        $this->mock_composer->expects($this->once())
            ->method('setRequiredVersions');

        $this->givenACommand();

        $data = ['require' => ['company/libx' => '2.0.0']];

        $result = $this->command->execute($data);

        $this->assertTrue($result);
    }

    private function givenACommand()
    {
        $this->command = new UpdateDependencies(
            $this->mock_repository,
            $this->mock_composer
        );
    }

    private function givenAMockComposer()
    {
        $this->mock_composer = $this->getMockBuilder('Sce\RepoMan\Domain\Composer')
            ->disableOriginalConstructor()
            ->getMock();
    }


    private function givenAMockRepository()
    {
        $this->mock_repository = $this->getMockBuilder('Sce\RepoMan\Domain\Repository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}