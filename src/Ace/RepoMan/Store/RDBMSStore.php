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
     * @param string $url
     * @param string $owner
     * @param string $description
     * @param string $lang
     * @param string $dependency_manager
     * @param string $timezone
     * @param int $active
     * @return bool
     */
    §
    {
        $statement = $this->client->prepare(
            sprintf('INSERT INTO %s (
                url, description, owner, lang, dependency_manager, timezone, active)
            VALUES (:url, :description, :owner, :lang, :dependency_manager, :timezone, :active)'
            , $this->table_name)
        );

        $result = $statement->execute(
            [
                ':url' => $url,
                ':description' => $description,
                ':owner' => $owner,
                ':lang' => $lang,
                ':dependency_manager' => $dependency_manager,
                ':timezone' => $timezone,
                ':active' => $active
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
