<?php

use Sce\RepoMan\View\DependencyReportJSONView;

/**
 * @group unit
 * @author timrodger
 * Date: 22/07/15
 */
class DependencyReportJSONViewTest extends PHPUnit_Framework_TestCase
{

    public function testRenderReturnsEmptyDocumentForNoData()
    {
        $view = new DependencyReportJSONView();
        $data = [];
        $body = $view->render($data);

        $this->assertSame(json_encode([]), $body);
    }

    public function testRenderReturnsJSONWithOneBlock()
    {
        $view = new DependencyReportJSONView();

        $data = [];
        $data['user/repo-a'] = [];
        $data['user/repo-a']['v1.0.0'] = [];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-x', 'config_version' => '~1.0', 'date' => '2015-07-10 06:54:46' , 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-c', 'config_version' => '1.0.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-z', 'config_version' => '1.*', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];

        $body = $view->render($data);

        $this->assertSame(json_encode($data), $body);

    }

    public function testRenderReturnsJSONWithTwoBlocks()
    {
        $view = new DependencyReportJSONView();

        $data = [];
        $data['user/repo-a'] = [];
        $data['user/repo-a']['v1.0.0'] = [];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-x', 'config_version' => '~1.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-c', 'config_version' => '1.0.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v2.0.0'] []= ['uri' => 'https://github.com/other/service-z', 'config_version' => '2.*', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];

        $body = $view->render($data);

        $this->assertSame(json_encode($data), $body);
    }
}