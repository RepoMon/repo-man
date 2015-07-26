<?php

use Sce\RepoMan\Command\UpdateComposerDependencies;

/**
 * @group unit
 * @author timrodger
 * Date: 26/07/15
 */
class UpdateComposerDependenciesUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Sce\RepoMan\Domain\Repository
     */
    private $mock_repository;

    public function testExecuteReturnsFalseIfUpdateFails()
    {
        $this->givenAMockRepository();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(false));

        $command = new UpdateComposerDependencies($this->mock_repository);

        $data = [];

        $result = $command->execute($data);
        $this->assertFalse($result);
    }

    public function testExecuteReturnsFalseIfComposerFilesAreMissing()
    {
        $this->givenAMockRepository();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->any())
            ->method('hasFile')
            ->will($this->returnValue(false));

        $command = new UpdateComposerDependencies($this->mock_repository);


        $data = [];

        $result = $command->execute($data);
        $this->assertFalse($result);
    }

    public function testExecuteReturnsFalseIfComposerFilesIsNotJson()
    {
        $this->givenAMockRepository();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->any())
            ->method('hasFile')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->any())
            ->method('getFile')
            ->will($this->returnValue('not json'));

        $command = new UpdateComposerDependencies($this->mock_repository);

        $data = [];

        $result = $command->execute($data);
        $this->assertFalse($result);
    }

    public function testExecute()
    {
        $this->givenAMockRepository();

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

        $json = json_encode(['require' => ['company/libx' => '1.0.0']]);

        $this->mock_repository->expects($this->any())
            ->method('hasFile')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->any())
            ->method('getFile')
            ->will($this->returnValue($json));

        $this->mock_repository->expects($this->once())
            ->method('setFile')
            ->with('composer.json', json_encode(['require' => ['company/libx' => '2.0.0']]));

        $this->mock_repository->expects($this->once())
            ->method('removeFile')
            ->with('composer.lock');

        $command = new UpdateComposerDependencies($this->mock_repository);

        $data = ['require' => ['company/libx' => '2.0.0']];

        $result = $command->execute($data);
    }

    private function givenAMockRepository()
    {
        $this->mock_repository = $this->getMockBuilder('Sce\RepoMan\Domain\Repository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}