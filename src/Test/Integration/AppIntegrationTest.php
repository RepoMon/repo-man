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
        putenv('REDIS_PORT=MEMORY');
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
        $url =  'https://github.com/timothy-r/render';

        $this->givenAClient();
        $this->client->request('POST', '/repositories', ['url' => $url]);

        $this->thenTheResponseIsSuccess();

    }

    public function testAddRepositoryFailsWhenUrlIsEmpty()
    {
        $this->givenAClient();
        $this->client->request('POST', '/repositories', ['url' => '']);

        $this->thenTheResponseIs400();
    }

    public function testAddRepositoryFailsWhenUrlIsMissing()
    {
        $this->givenAClient();
        $this->client->request('POST', '/repositories');

        $this->thenTheResponseIs400();
    }

    public function testUpdateRepositoriesSucceeds()
    {
        $this->givenAClient();
        $this->client->request('POST', '/repositories/update');

        $this->thenTheResponseIsSuccess();
    }

    public function testAddTokenSucceeds()
    {
        $host =  'github.com';
        $token = 'abcde12345';

        $this->givenAClient();
        $this->client->request('POST', '/tokens', ['host' => $host, 'token'=> $token]);

        $this->thenTheResponseIsSuccess();
    }

    public function testAddTokenFailsWhenHostIsMssing()
    {
        $token = 'abcde12345';

        $this->givenAClient();
        $this->client->request('POST', '/tokens', ['token'=> $token]);

        $this->thenTheResponseIs400();
    }

    public function testAddTokenFailsWhenTokenIsMssing()
    {
        $host =  'github.com';

        $this->givenAClient();
        $this->client->request('POST', '/tokens', ['host' => $host]);

        $this->thenTheResponseIs400();
    }

    public function testGetComposerReportSucceeds()
    {
        $this->givenAClient();
        $this->client->request('GET', '/reports/dependency/composer');

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

    private function thenTheResponseIs400()
    {
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    protected function assertResponseContents($expected_body)
    {
        $this->assertSame($expected_body, $this->client->getResponse()->getContent());
    }
}
