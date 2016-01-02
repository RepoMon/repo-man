<?php

use Ace\RepoMan\Store\RDBMSStore;

class PDOMock extends \PDO {
    public function __construct() {}
}

/**
 * @author timrodger
 * Date: 09/12/15
 */
class RDBMSStoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    private $client;

    /**
     * @var string
     */
    private $table_name = 'repositories';

    /**
     * @var string
     */
    private $directory = '/';

    /**
     * @var RDBMSStore
     */
    private $store;

    private function givenAClient()
    {
        $this->client = $this->getMockBuilder('PDOMock')
            ->getMock();
    }

    private function givenAStore()
    {
        $this->store = new RDBMSStore($this->client, $this->table_name, $this->directory);
    }

    /**
     * @dataProvider getAddData
     *
     * @param $hour
     * @param $frequency
     * @param $timezone
     */
    public function testAdd($url, $owner, $description, $lang, $dependency_manager, $timezone, $active)
    {

        $this->givenAClient();

        $this->givenAStore();

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO ' . $this->table_name . ' (
                url, description, owner, lang, dependency_manager, timezone, active)
            VALUES (:url, :description, :owner, :lang, :dependency_manager, :timezone, :active)')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([
                ':url' => $url,
                ':description' => $description,
                ':owner' => $owner,
                ':lang' => $lang,
                ':dependency_manager' => $dependency_manager,
                ':timezone' => $timezone,
                ':active' => $active
            ]);

        $this->store->add($url, $owner, $description, $lang, $dependency_manager, $timezone, $active);
    }

    public function getAddData()
    {
        return [
            ['test/repo-a', 'malcolm-x', 'A test repo', 'PHP', 'composer', 'Europe/London', 1],
        ];
    }

    public function testGetRepositoryByUrl()
    {
        $url = 'owner/repo';

        $this->givenAClient();

        $this->givenAStore();

        $result = [
                'url' => $url,
                'owner' => 'xavier',
                'language' => 'PHP7',
                'dependency_manager' => 'composer'
        ];

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM ' .$this->table_name. ' WHERE url = :url')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([':url' => $url]);

        $mock_statement->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($result));

        $repository = $this->store->get($url);

        $this->assertSame($result, $repository);

    }

    public function testGetAllRepositoriesForOwner()
    {
        $this->givenAClient();

        $this->givenAStore();

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM ' . $this->table_name . ' WHERE owner = :owner')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([':owner' => 'malcolm-q']);

        $mock_statement->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(['owner/repo']));

        $repositories = $this->store->getAll('malcolm-q');

        $this->assertSame(['owner/repo'], $repositories);
    }

    public function testDeleteReturnsTrueOnSuccess()
    {
        $url = '/test/repo';

        $this->givenAClient();

        $this->givenAStore();

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM ' . $this->table_name . ' WHERE url = :url')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([':url' => $url])
            ->will($this->returnValue(true));

        $result = $this->store->delete($url);

        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseOnFailure()
    {
        $url = '/test/repo';

        $this->givenAClient();

        $this->givenAStore();

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM ' . $this->table_name . ' WHERE url = :url')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([':url' => $url])
            ->will($this->returnValue(false));

        $result = $this->store->delete($url);

        $this->assertFalse($result);
    }
}
