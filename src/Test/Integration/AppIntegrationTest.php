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
    /**
     * @var Symfony\Component\HttpKernel\Client
     */
    private $client;

    public function createApplication()
    {
        putenv('DB_TYPE=MEMORY');
        return require __DIR__.'/../../app.php';
    }

    public function testListRepositoriesReturnsAJsonArray()
    {
        $this->givenAClient();
        $owner = 'dave';
        $this->client->request('GET', '/repositories/' . $owner);

        $this->thenTheResponseIsSuccess();

        $body = $this->client->getResponse()->getContent();

        $this->assertTrue(is_array(json_decode($body, 1)));
    }

    private function givenAClient()
    {
        $this->client = $this->createClient();
    }

    private function thenTheResponseIsSuccess()
    {
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    private function thenTheResponseIs400()
    {
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
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
