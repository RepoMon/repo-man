<?php

use Sce\RepoMan\Git\Store;

/**
 * @author timrodger
 * Date: 18/07/15
 */
class GitStoreUnitTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Sce\RepoMan\Git\Store
     */
    private $store;

    /**
     * @var Predis\Client
     */
    private $mock_client;

    /**
     * @var Sce\RepoMan\Configuration
     */
    private $mock_config;

    /**
     *
     */
    public function testGetAll()
    {
        $dir = '/tmp/repos';
        $this->givenAMockConfig($dir);
        $this->givenAMockClient();
        $this->givenAStore();

        $this->givenSomeMockedData([
            ['url' => 'https://github.com/user/repo-name',
            'path' => '/tmp/repos/repo-name']
        ]);

        $all_repos = $this->store->getAll();
        $this->assertTrue(is_array($all_repos));
        $this->assertSame(1, count($all_repos));
    }

    /**
     *
     */
    public function testGetAllReturnsAnEmptyArrayWhenNoRepositoriesExist()
    {
        $dir = '/tmp/repos';
        $this->givenAMockConfig($dir);
        $this->givenAMockClient();
        $this->givenAStore();

        $all_repos = $this->store->getAll();
        $this->assertTrue(is_array($all_repos));
        $this->assertSame(0, count($all_repos));
    }

    private function givenSomeMockedData(array $repos)
    {
        // set up some contents of the mock redis instance
        foreach($repos as $repo) {

            $this->mock_client->expects($this->once())
                ->method('smembers')
                ->with(Store::REPO_SET_NAME)
                ->will($this->returnValue([$repo['url']]));

            $this->mock_client->expects($this->once())
                ->method('hmget')
                ->with($repo['url'])
                ->will($this->returnValue([$repo['url'], $repo['path']]));
        }
    }

    /**
     *
     */
    private function givenAStore()
    {
        $this->store = new Store($this->mock_config, $this->mock_client);
    }

    /**
     * @param $dir
     * @param array $repos
     */
    private function givenAMockConfig($dir)
    {
        $this->mock_config = $this->getMockBuilder('Sce\RepoMan\Configuration')
            ->setMethods(['getRepoDir', 'getRepositoryNames'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->mock_config->expects($this->any())
            ->method('getRepoDir')
            ->will($this->returnValue($dir));
    }

    private function givenAMockClient()
    {
        $this->mock_client = $this->getMockBuilder('Predis\Client')
            ->setMethods(['hmset', 'hmget', 'sadd', 'smembers'])
            ->disableOriginalConstructor()
            ->getMock();
    }
}