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
            'https://github.com/user/repo-name'
        ]);

        $result = $this->store->getAll();
        $this->assertTrue(is_array($result));
        $this->assertSame(1, count($result));
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

        $result = $this->store->getAll();
        $this->assertTrue(is_array($result));
        $this->assertSame(0, count($result));
    }

    public function testGetAllReturnEmptyArrayWhenServerIsUnavailable()
    {
        $dir = '/tmp/repos';
        $this->givenAMockConfig($dir);
        $this->givenAMockClient();
        $this->givenAStore();

        $this->mock_client->expects($this->once())
            ->method('smembers')
            ->with(Store::REPO_SET_NAME)
            ->will($this->throwException(new \Predis\Response\ServerException));

        $result = $this->store->getAll();
        $this->assertTrue(is_array($result));
        $this->assertSame(0, count($result));
    }

    public function testAddRepository()
    {
        $dir = '/tmp/repos';
        $this->givenAMockConfig($dir);
        $this->givenAMockClient();
        $this->givenAStore();

        $url = 'https://github.com/user/repo-name';

        $this->mock_client->expects($this->once())
            ->method('sadd')
            ->with(Store::REPO_SET_NAME, $url);

        $repository = $this->store->add($url);
        $this->assertInstanceOf('Sce\RepoMan\Git\Repository', $repository);
    }

    private function givenSomeMockedData(array $repos)
    {
        // set up some contents of the mock redis instance
        $this->mock_client->expects($this->once())
            ->method('smembers')
            ->with(Store::REPO_SET_NAME)
            ->will($this->returnValue($repos));
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