<?php

use Sce\RepoMan\Report\DependencyConsistencyReport;
use \Sce\Test\RepositoryMockTrait;

/**
 * @author timrodger
 * Date: 19/08/15
 */
class DependencyConsistencyReportUnitTest
{
    use RepositoryMockTrait;

    public function testReportWithOneRepoIsEmpty()
    {
        $report = new DependencyConsistencyReport();
    }
}