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

    public function testReportForInconsistentRepositories()
    {
        $this->givenADependency("https://zithub.com/cool-toys/goo|v1.4.0", "framework/mvc", "2.*", "2.7.0");
        $this->givenADependency("https://zithub.com/cool-toys/goo-ui|v2.3.1", "framework/mvc", "1.*", "1.12.0");

        $this->givenAMockStore();

        $report = new DependencyConsistencyReport($this->mock_store);

        $results = $report->generate();

        $this->assertTrue(is_array($results));

        $this->assertTrue(in_array(['uri' => "https://zithub.com/cool-toys/goo", "requested" => "2.*", "actual" => "2.7.0"], $results["framework/mvc"]));
        $this->assertTrue(in_array(['uri' => "https://zithub.com/cool-toys/goo-ui", "requested" => "1.*", "actual" => "1.12.0"], $results["framework/mvc"]));

    }
}