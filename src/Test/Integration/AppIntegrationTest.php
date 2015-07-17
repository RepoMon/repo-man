<?php

use Silex\WebTestCase;

/**
 * @group integration
 *
 * @author timrodger
 * Date: 18/03/15
 */
class AppIntegrationTest extends WebTestCase
{
    private $client;

    public function createApplication()
    {
        return require __DIR__.'/../../app.php';
    }

    public function testGetRootSucceeds()
    {
        $this->givenAClient();
        $this->client->request('GET', '/');

        $this->thenTheResponseIsSuccess();
    }

    public function testListRepositoriesReturnsAJsonArray()
    {
        $this->givenAClient();
        $this->client->request('GET', '/repositories');

        $this->thenTheResponseIsSuccess();

        $body = $this->client->getResponse()->getContent();

        $this->assertTrue(is_array(json_decode($body, 1)));
    }

    public function testAddRepositorySucceeds()
    {
        $name =  'https://github.com/timothy-r/render';

        $this->givenAClient();
        $this->client->request('PUT', '/repositories/' . rawurlencode($name));

        $this->thenTheResponseIsSuccess();

        var_dump($this->client->getResponse()->getContent());
    }

    public function testUpdateRepositoriesSucceeds()
    {
        $this->givenAClient();
        $this->client->request('POST', '/repositories/update');

        $this->thenTheResponseIsSuccess();
    }

    private function givenAClient()
    {
        $this->client = $this->createClient();
    }

    private function thenTheResponseIsSuccess()
    {
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    private function thenTheResponseIs404()
    {
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    protected function assertResponseContents($expected_body)
    {
        $this->assertSame($expected_body, $this->client->getResponse()->getContent());
    }
}
