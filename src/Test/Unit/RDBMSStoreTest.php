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
     * @param $url
     * @param $full_name
     * @param $owner
     * @param $description
     * @param $lang
     * @param $dependency_manager
     * @param $timezone
     * @param $active
     */
    public function testAdd($url, $full_name, $owner, $description, $lang, $dependency_manager, $timezone, $active)
    {

        $this->givenAClient();

        $this->givenAStore();

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO ' . $this->table_name . ' (
                url, full_name, description, owner, lang, dependency_manager, timezone, active)
            VALUES (:url, :full_name, :description, :owner, :lang, :dependency_manager, :timezone, :active)')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([
                ':url' => $url,
                ':full_name' => $full_name,
                ':description' => $description,
                ':owner' => $owner,
                ':lang' => $lang,
                ':dependency_manager' => $dependency_manager,
                ':timezone' => $timezone,
                ':active' => $active
            ]);

        $this->store->add($url, $full_name, $owner, $description, $lang, $dependency_manager, $timezone, $active);
    }

    public function getAddData()
    {
        return [
            ['https://github.com/test/repo-a', 'test/repo-a', 'malcolm-x', 'A test repo', 'PHP', 'composer', 'Europe/London', 1],
        ];
    }

    public function testGetRepository()
    {
        $full_name = 'owner/repo';

        $this->givenAClient();

        $this->givenAStore();

        $result = [
                'full_name' => $full_name,
                'owner' => 'xavier',
                'language' => 'PHP',
                'dependency_manager' => 'composer'
        ];

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM ' .$this->table_name. ' WHERE full_name = :full_name')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([':full_name' => $full_name]);

        $mock_statement->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($result));

        $repository = $this->store->get($full_name);

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
        $full_name = '/test/repo';

        $this->givenAClient();

        $this->givenAStore();

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM ' . $this->table_name . ' WHERE full_name = :full_name')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([':full_name' => $full_name])
            ->will($this->returnValue(true));

        $result = $this->store->delete($full_name);

        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseOnFailure()
    {
        $full_name = '/test/repo';

        $this->givenAClient();

        $this->givenAStore();

        $mock_statement = $this->getMockBuilder('PDOStatement')
            ->getMock();

        $this->client->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM ' . $this->table_name . ' WHERE full_name = :full_name')
            ->will($this->returnValue($mock_statement));

        $mock_statement->expects($this->once())
            ->method('execute')
            ->with([':full_name' => $full_name])
            ->will($this->returnValue(false));

        $result = $this->store->delete($full_name);

        $this->assertFalse($result);
    }
}
