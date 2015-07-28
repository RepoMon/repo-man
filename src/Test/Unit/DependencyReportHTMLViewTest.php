<?php

use Sce\RepoMan\View\DependencyReportHTMLView;

/**
 * @group unit
 * @author timrodger
 * Date: 22/07/15
 */
class ComposerDependencyReportHTMLViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private  $header = '<thead><tr><th>Library</th><th>Version</th><th>Used By</th><th>Configured Version</th><th>Last Updated</th></tr></thead>';

    public function testRenderReturnsHeaderForNoData()
    {
        $view = new DependencyReportHTMLView();
        $data = [];
        $body = $view->render($data);

        $this->assertSame("<table>\n".$this->header."\n</table>", $body);
    }

    public function testRenderReturnsHTMLWithOneBlock()
    {
        $view = new DependencyReportHTMLView();

        $data = [];
        $data['user/repo-a'] = [];
        $data['user/repo-a']['v1.0.0'] = [];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-x', 'config_version' => '~1.0', 'date' => '2015-07-10 06:54:46' , 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-c', 'config_version' => '1.0.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-z', 'config_version' => '1.*', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];

        $body = $view->render($data);

        $expected =  '<table>
<thead><tr><th>Library</th><th>Version</th><th>Used By</th><th>Configured Version</th><th>Last Updated</th></tr></thead>
<tr><td>user/repo-a</td><td>v1.0.0</td><td>https://github.com/other/service-x:1.0.0</td><td>~1.0</td><td>2015-07-10 06:54:46</td>/<tr>
<tr><td></td><td></td><td>https://github.com/other/service-c:1.0.0</td><td>1.0.0</td><td>2015-07-10 06:54:46</td>/<tr>
<tr><td></td><td></td><td>https://github.com/other/service-z:1.0.0</td><td>1.*</td><td>2015-07-10 06:54:46</td>/<tr>
<tr><td></td><td></td><td></td>/<tr>
</table>';
        $this->assertSame($expected, $body);
    }

    public function testRenderReturnsHTMLWithTwoBlocks()
    {
        $view = new DependencyReportHTMLView();

        $data = [];
        $data['user/repo-a'] = [];
        $data['user/repo-a']['v1.0.0'] = [];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-x', 'config_version' => '~1.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v1.0.0'] []= ['uri' => 'https://github.com/other/service-c', 'config_version' => '1.0.0', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];
        $data['user/repo-a']['v2.0.0'] []= ['uri' => 'https://github.com/other/service-z', 'config_version' => '2.*', 'date' => '2015-07-10 06:54:46', 'latest_tag' => '1.0.0'];

        $body = $view->render($data);

        $expected =  '<table>
<thead><tr><th>Library</th><th>Version</th><th>Used By</th><th>Configured Version</th><th>Last Updated</th></tr></thead>
<tr><td>user/repo-a</td><td>v1.0.0</td><td>https://github.com/other/service-x:1.0.0</td><td>~1.0</td><td>2015-07-10 06:54:46</td>/<tr>
<tr><td></td><td></td><td>https://github.com/other/service-c:1.0.0</td><td>1.0.0</td><td>2015-07-10 06:54:46</td>/<tr>
<tr><td></td><td>v2.0.0</td><td>https://github.com/other/service-z:1.0.0</td><td>2.*</td><td>2015-07-10 06:54:46</td>/<tr>
<tr><td></td><td></td><td></td>/<tr>
</table>';
        $this->assertSame($expected, $body);
    }
}