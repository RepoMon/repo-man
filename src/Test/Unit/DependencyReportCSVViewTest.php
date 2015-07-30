<?php

use Sce\RepoMan\View\DependencyReportCSVView;

/**
 * @group unit
 * @author timrodger
 * Date: 22/07/15
 */
class DependencyReportCSVViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private  $header = 'Library,Version,"Used By","Configured Version","Last Updated"';

    public function testRenderReturnsHeaderForNoData()
    {
        $view = new DependencyReportCSVView();
        $data = [];
        $body = $view->render($data);

        $this->assertSame($this->header, $body);
    }

    public function testRenderReturnsCSVWithOneBlock()
    {
        $view = new DependencyReportCSVView();

        $data = [];
        $data['user/repo-a'] = [];
        $data['user/repo-a']['v1.0.0'] = [];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-x', 'config_version' => '~1.0', 'date' => '2015-07-10 06:54:46' , 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-c', 'config_version' => '1.0.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-z', 'config_version' => '1.*', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];

        $body = $view->render($data);

        $expected =  'Library,Version,"Used By","Configured Version","Last Updated"
user/repo-a,v1.0.0,https://github.com/other/service-x:1.0.0,~1.0,"2015-07-10 06:54:46"
,,https://github.com/other/service-c:1.0.0,1.0.0,"2015-07-10 06:54:46"
,,https://github.com/other/service-z:1.0.0,1.*,"2015-07-10 06:54:46"
,,';
        $this->assertSame($expected, $body);
    }

    public function testRenderReturnsCSVWithTwoBlocks()
    {
        $view = new DependencyReportCSVView();

        $data = [];
        $data['user/repo-a'] = [];
        $data['user/repo-a']['v1.0.0'] = [];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-x', 'config_version' => '~1.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-c', 'config_version' => '1.0.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v2.0.0'] []= ['uri' => 'https://github.com/other/service-z', 'config_version' => '2.*', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];

        $body = $view->render($data);

        $expected =  'Library,Version,"Used By","Configured Version","Last Updated"
user/repo-a,v1.0.0,https://github.com/other/service-x:1.0.0,~1.0,"2015-07-10 06:54:46"
,,https://github.com/other/service-c:1.0.0,1.0.0,"2015-07-10 06:54:46"
,v2.0.0,https://github.com/other/service-z:1.0.0,2.*,"2015-07-10 06:54:46"
,,';
        $this->assertSame($expected, $body);
    }
}