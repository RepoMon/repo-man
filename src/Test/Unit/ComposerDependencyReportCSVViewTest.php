<?php

use Sce\RepoMan\View\ComposerDependencyReportCSVView;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class ComposerDependencyReportCSVViewTest extends PHPUnit_Framework_TestCase
{

    public function testRenderReturnsEmptyStringForNoData()
    {
        $view = new ComposerDependencyReportCSVView();
        $data = [];
        $body = $view->render($data);

        $this->assertSame('', $body);
    }
}