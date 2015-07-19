<?php

use Silex\WebTestCase;

/**
 * @group integration
 *
 * @author timrodger
 * Date: 18/03/15
 */
class AppErrorIntegrationTest extends WebTestCase
{
    private $client;

    public function createApplication()
    {
        putenv('REDIS_PORT=tcp://172.17.0.154:9999');
        return require __DIR__.'/../../app.php';
    }

    public function testGetRootSucceeds()
    {
        $this->givenAClient();
        $this->client->request('GET', '/');

        $this->thenTheResponseIsSuccess();
    }

    public function testListRepositoriesFails()
    {
        $this->givenAClient();
        $this->client->request('GET', '/repositories');

        $this->thenTheResponseIs500();
    }

    public function testAddRepositoryFails()
    {
        $url =  'https://github.com/timothy-r/render';

        $this->givenAClient();
        $this->client->request('POST', '/repositories', ['url' => $url]);

        $this->thenTheResponseIs500();
    }

    public function testUpdateRepositoriesFails()
    {
        $this->givenAClient();
        $this->client->request('POST', '/repositories/update');

        $this->thenTheResponseIs500();
    }

    private function givenAClient()
    {
        $this->client = $this->createClient();
    }

    private function thenTheResponseIsSuccess()
    {
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    private function thenTheResponseIs500()
    {
        $this->assertSame(500, $this->client->getResponse()->getStatusCode());
    }

    protected function assertResponseContents($expected_body)
    {
        $this->assertSame($expected_body, $this->client->getResponse()->getContent());
    }
}
