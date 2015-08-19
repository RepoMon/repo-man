<?php

use Sce\RepoMan\Report\DependencyConsistencyReport;
use \Sce\Test\RepositoryMockTrait;

/**
 * @author timrodger
 * Date: 19/08/15
 */
class DependencyConsistencyReportUnitTest extends PHPUnit_Framework_TestCase
{
    use RepositoryMockTrait;

    public function testReportWithOneRepositoryIsEmpty()
    {
        $this->givenADependency("https://zithub.com/cool-toys/goo|v1.0.0", "framework/mvc", "2.*", "2.7.0");

        $this->givenAMockStore();

        $report = new DependencyConsistencyReport($this->mock_store);

        $results = $report->generate();

        $this->assertNull($results);
    }

    public function testReportForConsistentRepositoriesIsEmpty()
    {
        $this->givenADependency("https://zithub.com/cool-toys/goo|v1.4.0", "framework/mvc", "2.*", "2.7.0");
        $this->givenADependency("https://zithub.com/cool-toys/goo-ui|v2.3.1", "framework/mvc", "2.*", "2.7.0");

        $this->givenAMockStore();

        $report = new DependencyConsistencyReport($this->mock_store);

        $results = $report->generate();

        $this->assertNull($results);
    }

    public function testReportForInconsistentRepositoriesHasInformation()
    {
        $this->givenADependency("https://zithub.com/cool-toys/goo|v1.4.0", "framework/mvc", "2.*", "2.7.0");
        $this->givenADependency("https://zithub.com/cool-toys/goo-ui|v2.3.1", "framework/mvc", "1.*", "1.12.0");

        $this->givenAMockStore();

        $report = new DependencyConsistencyReport($this->mock_store);

        $results = $report->generate();

        $this->assertTrue(is_array($results));
    }
}