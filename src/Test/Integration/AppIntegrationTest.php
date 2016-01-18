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

    public function testListRepositoriesForOwnerReturnsAJsonArray()
    {
        $this->givenAClient();
        $owner = 'dave';
        $this->client->request('GET', '/repositories?' . $owner);

        $this->thenTheResponseIsSuccess();

        $body = $this->client->getResponse()->getContent();

        $this->assertTrue(is_array(json_decode($body, 1)));
    }

    public function testGetRepositoryReturnsErrorWhenMissing()
    {
        $this->givenAClient();
        $this->client->request('GET', '/repositories/vendor/library');

        $this->thenTheResponseIs(404);
    }

    public function xtestGetRepository()
    {
        $this->givenAClient();

        $this->app['store']->add(
            'https://github.com/vendor/library',
            'vendor/library',
            'owner',
            'A test repo',
            'javascript',
            'npm',
            'Europe/London',
            false
            );

        $this->client->request('GET', '/repositories/vendor/library');

        $this->thenTheResponseIs(200);
    }

    private function givenAClient()
    {
        $this->client = $this->createClient();
    }

    private function thenTheResponseIsSuccess()
    {
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    private function thenTheResponseIs($code)
    {
        $this->assertSame($code, $this->client->getResponse()->getStatusCode());
    }

    protected function assertResponseContents($expected_body)
    {
        $this->assertSame($expected_body, $this->client->getResponse()->getContent());
    }
}
