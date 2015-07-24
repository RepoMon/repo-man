<?php

use Sce\RepoMan\Report\ComposerDependencyReport;

/**
 * @group unit
 * @author timrodger
 * Date: 20/07/15
 */
class ComposerDependencyReportUnitTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var array
     */
    private $repositories = [];

    /**
     * @var Sce\RepoMan\Store\StoreInterface
     */
    private $mock_store;


    public function setUp()
    {
        parent::setUp();
        $this->repositories = [];
    }

    public function testGenerateForOneRepository()
    {
        // create a mock repository
        $name = 'widgets/cool';
        $uri = 'https://github.com/user/service-name';
        $latest_tag = 'v2.0.4';

        $version = '~1.1';
        $config_data = ['require' => [$name => $version]];

        $lock_version = '1.3.1';
        $time = "2015-07-10 06:54:46";
        $lock_data = ["packages-dev" => [
            ['name' => $name, 'version' => $lock_version, 'time' => $time]
        ]];

        $this->givenAMockRepository($uri, json_encode($config_data), json_encode($lock_data), $latest_tag);

        // get mock store
        $this->givenAMockStore();

        $this->report = new ComposerDependencyReport($this->mock_store);

        $result = $this->report->generate();

        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists($name, $result));
        $this->assertTrue(array_key_exists($lock_version, $result[$name]));
        $this->assertSame($uri, $result[$name][$lock_version][0]['uri']);
        $this->assertSame($version, $result[$name][$lock_version][0]['config_version']);
        $this->assertSame($time, $result[$name][$lock_version][0]['date']);
        $this->assertSame($latest_tag, $result[$name][$lock_version][0]['latest_tag']);
    }

    public function testGenerateForMoreThanOneRepository()
    {
        // create a mock repository
        $name = 'widgets/cool';
        $uri = 'https://github.com/user/service-name';
        $latest_tag = 'v2.0.4';

        $version_a = '~1.1';
        $config_data = ['require' => [$name => $version_a]];

        $lock_version_a = '1.3.1';
        $time_a = "2015-07-10 06:54:46";
        $lock_data = ["packages-dev" => [
            ['name' => $name, 'version' => $lock_version_a, 'time' => $time_a]
        ]];

        $this->givenAMockRepository($uri, json_encode($config_data), json_encode($lock_data), $latest_tag);

        $version_b = '~2.3';
        $config_data = ['require' => [$name => $version_b]];

        $lock_version_b = '2.4.9';
        $time_b = "2015-07-20 02:12:00";
        $lock_data = ["packages-dev" => [
            ['name' => $name, 'version' => $lock_version_b, 'time' => $time_b]
        ]];

        $this->givenAMockRepository($uri, json_encode($config_data), json_encode($lock_data), $latest_tag);

        // get mock store
        $this->givenAMockStore();

        $this->report = new ComposerDependencyReport($this->mock_store);

        $result = $this->report->generate();

        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists($name, $result));

        $this->assertTrue(array_key_exists($lock_version_a, $result[$name]));
        $this->assertSame($uri, $result[$name][$lock_version_a][0]['uri']);
        $this->assertSame($version_a, $result[$name][$lock_version_a][0]['config_version']);
        $this->assertSame($time_a, $result[$name][$lock_version_a][0]['date']);
        $this->assertSame($latest_tag, $result[$name][$lock_version_a][0]['latest_tag']);

        $this->assertTrue(array_key_exists($lock_version_b, $result[$name]));
        $this->assertSame($uri, $result[$name][$lock_version_b][0]['uri']);
        $this->assertSame($version_b, $result[$name][$lock_version_b][0]['config_version']);
        $this->assertSame($time_b, $result[$name][$lock_version_b][0]['date']);
        $this->assertSame($latest_tag, $result[$name][$lock_version_b][0]['latest_tag']);
    }

    private function givenAMockStore()
    {
        $this->mock_store = $this->getMockBuilder('Sce\RepoMan\Store\StoreInterface')
            ->getMock();

        $this->mock_store->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($this->repositories));
    }

    private function givenAMockRepository($url, $config_json, $lock_json, $latest_tag)
    {
        $mock_repository = $this->getMockBuilder('Sce\RepoMan\Domain\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $mock_repository->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url));

        $mock_repository->expects($this->at(0))
            ->method('getFile')
            ->with('composer.json')
            ->will($this->returnValue($config_json));

        $mock_repository->expects($this->at(1))
            ->method('getFile')
            ->with('composer.lock')
            ->will($this->returnValue($lock_json));

        $mock_repository->expects($this->any())
            ->method('getLatestTag')
            ->will($this->returnValue($latest_tag));

        $this->repositories []= $mock_repository;
    }
}