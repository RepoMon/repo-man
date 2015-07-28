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

    /**
     * @var \Sce\RepoMan\Domain\CommandLine
     */
    private $mock_command_line;

    public function testExecuteReturnsFalseIfUpdateFails()
    {
        $this->givenAMockRepository();
        $this->givenAMockCommandLine();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(false));

        $this->givenACommand();

        $data = [];

        $result = $this->command->execute($data);
        $this->assertFalse($result);
    }

    public function testExecuteReturnsFalseIfComposerFilesAreMissing()
    {
        $this->givenAMockRepository();
        $this->givenAMockCommandLine();

        $this->mock_repository->expects($this->once())
            ->method('update')
            ->will($this->returnValue(true));

        $this->mock_repository->expects($this->any())
            ->method('hasFile')
            ->will($this->returnValue(false));

        $this->givenACommand();

        $data = [];

        $result = $this->command->execute($data);
        $this->assertFalse($result);
    }

    public function testExecuteReturnsFalseIfComposerFilesIsNotJson()
    {
        $this->givenAMockRepository();
        $this->givenAMockCommandLine();

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

        $result = $this->command->execute($data);
        $this->assertFalse($result);
    }

    public function testExecute()
    {
        $this->givenAMockRepository();
        $this->givenAMockCommandLine();

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
            ->with('composer.json', json_encode(['require' => ['company/libx' => '2.0.0']], JSON_PRETTY_PRINT));

        $this->mock_repository->expects($this->once())
            ->method('removeFile')
            ->with('composer.lock');

        $this->mock_command_line->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(true));

        $this->givenACommand();

        $data = ['require' => ['company/libx' => '2.0.0']];

        $result = $this->command->execute($data);

        $this->assertTrue($result);
    }

    private function givenACommand()
    {
        $this->command = new UpdateComposerDependencies(
            $this->mock_repository,
            $this->mock_command_line
        );
    }

    private function givenAMockCommandLine()
    {
        $this->mock_command_line = $this->getMockBuilder('Sce\RepoMan\Domain\CommandLine')
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