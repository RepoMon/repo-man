<?php

use Sce\RepoMan\Git\RepositoryCollection;

/**
 * @group unit
 * @author timrodger
 * Date: 17/07/15
 */
class GitRepoCollectionUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Sce\RepoMan\Configuration
     */
    private $mock_config;

    public function testGetRepositories()
    {
        $dir = '/tmp';
        $repos = ['https://github.com/user/repo-1', 'https://github.com/user/repo-2'];

        $this->givenAMockConfig($dir, $repos);
        $collection = new RepositoryCollection($this->mock_config);

        $repos = $collection->getRepositories();

        $this->assertTrue(is_array($repos));
        $this->assertTrue(2 == count($repos));
        foreach($repos as $repo){
            $this->assertInstanceOf('Sce\RepoMan\Git\Repository', $repo);
        }
    }


    private function givenAMockConfig($dir, array $repos)
    {
        $this->mock_config = $this->getMockBuilder('Sce\RepoMan\Configuration')
            ->setMethods(['getRepoDir', 'getRepositoryNames'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->mock_config->expects($this->any())
            ->method('getRepoDir')
            ->will($this->returnValue($dir));

        $this->mock_config->expects($this->any())
            ->method('getRepositoryNames')
            ->will($this->returnValue($repos));
    }
}