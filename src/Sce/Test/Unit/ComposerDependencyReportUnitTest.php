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
        $dependency = 'widgets/cool';
        $uri = 'https://github.com/user/service-name';
        $latest_tag = 'v2.0.4';

        $version = '~1.1';

        $lock_version = '1.3.1';

        $this->givenADependency($uri.'|'.$latest_tag, $dependency, $version, $lock_version);

        // get mock store
        $this->givenAMockStore();

        $this->report = new ComposerDependencyReport($this->mock_store);

        $result = $this->report->generate();

        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists($dependency, $result));
        $this->assertTrue(array_key_exists($lock_version, $result[$dependency]));
        $this->assertSame($uri, $result[$dependency][$lock_version][0]['uri']);
        $this->assertSame($version, $result[$dependency][$lock_version][0]['config_version']);
        $this->assertSame($latest_tag, $result[$dependency][$lock_version][0]['latest_tag']);
    }

    public function testGenerateForMoreThanOneRepository()
    {
        // create a mock repository
        $dependency = 'widgets/cool';
        $uri = 'https://github.com/user/service-name';
        $latest_tag = 'v2.0.4';

        $version_a = '~1.1';

        $lock_version_a = '1.3.1';

        $this->givenADependency($uri.'|'.$latest_tag, $dependency, $version_a, $lock_version_a);

        $version_b = '~2.3';

        $lock_version_b = '2.4.9';

        $this->givenADependency($uri.'|'.$latest_tag, $dependency, $version_b, $lock_version_b);

        // get mock store
        $this->givenAMockStore();

        $this->report = new ComposerDependencyReport($this->mock_store);

        $result = $this->report->generate();

        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists($dependency, $result));

        $this->assertTrue(array_key_exists($lock_version_a, $result[$dependency]));
        $this->assertSame($uri, $result[$dependency][$lock_version_a][0]['uri']);
        $this->assertSame($version_a, $result[$dependency][$lock_version_a][0]['config_version']);
        $this->assertSame($latest_tag, $result[$dependency][$lock_version_a][0]['latest_tag']);

        $this->assertTrue(array_key_exists($lock_version_b, $result[$dependency]));
        $this->assertSame($uri, $result[$dependency][$lock_version_b][0]['uri']);
        $this->assertSame($version_b, $result[$dependency][$lock_version_b][0]['config_version']);
        $this->assertSame($latest_tag, $result[$dependency][$lock_version_b][0]['latest_tag']);
    }

}