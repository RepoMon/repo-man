<?php

use Sce\RepoMan\Report\ComposerDependencyReport;
use Sce\Test\RepositoryMockTrait;


/**
 * @group unit
 * @author timrodger
 * Date: 20/07/15
 */
class ComposerDependencyReportUnitTest extends PHPUnit_Framework_TestCase
{
    use RepositoryMockTrait;


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

}