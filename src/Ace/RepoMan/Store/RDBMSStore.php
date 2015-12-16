<?php namespace Ace\RepoMan\Store;

use PDO;

/**
 * @author timrodger
 * Date: 09/12/15
 */
class RDBMSStore implements StoreInterface
{

    /**
     * @var PDO
     */
    private $client;

    /**
     * @var string
     */
    private $table_name;

    /**
     * @var string
     */
    private $directory;

    /**
     * @param PDO $client
     * @param string $table_name
     * @param string $directory
     */
    public function __construct(PDO $client, $table_name, $directory)
    {
        $this->client = $client;
        $this->table_name = $table_name;
        $this->directory = $directory;
    }

    /**
     * @param $url
     * @param $owner
     * @param $language
     * @param $dependency_manager
     * @return bool
     */
    public function add($url, $owner, $language, $dependency_manager)
    {
        $statement = $this->client->prepare('INSERT INTO ' . $this->table_name . ' (url, owner, lang, dependency_manager) VALUES(:url, :owner, :lang, :dependency_manager)');

        $result = $statement->execute(
            [
                ':url' => $url,
                ':owner' => $owner,
                ':lang' => $language,
                ':dependency_manager' => $dependency_manager
            ]
        );

        $statement->closeCursor();

        return $result;
    }

    /**
     * @param $url
     * @param $token
     * @return array
     */
    public function get($url)
    {

        $statement = $this->client->prepare('SELECT * FROM ' . $this->table_name . ' WHERE url = :url');

        $statement->execute(
            [
                ':url' => $url
            ]
        );

        return $statement->fetch();
    }

    /**
     * @param $repository
     */
    public function getAll($owner)
    {
        $statement = $this->client->prepare('SELECT * FROM ' . $this->table_name . ' WHERE owner = :owner');
        $statement->execute(
            [
                ':owner' => $owner
            ]
        );

        return $statement->fetchAll();
    }

    /**
     * @param $name
     * @return boolean
     */
    public function delete($url)
    {
        $statement = $this->client->prepare('DELETE FROM ' . $this->table_name . ' WHERE url = :url');
        return $statement->execute(
            [
                ':url' => $url
            ]
        );
    }
}
