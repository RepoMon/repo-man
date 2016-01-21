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
     * Branch name to use until we allow configuration
     * @var string
     */
    private $branch = 'master';

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
     * @param string $full_name
     * @param string $owner
     * @param string $description
     * @param string $lang
     * @param string $dependency_manager
     * @param string $timezone
     * @param boolean $active
     * @param boolean $is_private
     * @return bool
     */
    public function add($url, $full_name, $owner, $description, $lang, $dependency_manager, $timezone, $active, $is_private)
    {
        $statement = $this->client->prepare(
            sprintf('INSERT INTO %s (
                url, full_name, description, owner, lang, dependency_manager, timezone, active, branch, private)
            VALUES (:url, :full_name, :description, :owner, :lang, :dependency_manager, :timezone, :active, :branch, :private)'
            , $this->table_name)
        );

        $result = $statement->execute(
            [
                ':url' => $url,
                ':full_name' => $full_name,
                ':description' => $description,
                ':owner' => $owner,
                ':lang' => $lang,
                ':dependency_manager' => $dependency_manager,
                ':timezone' => $timezone,
                ':active' => $active ? 1 : 0,
                ':branch' => $this->branch,
                ':private' => $is_private ? 1 : 0
            ]
        );

        $statement->closeCursor();

        return $result;
    }

    /**
     * @param $full_name
     * @return array
     */
    public function get($full_name)
    {
        $statement = $this->client->prepare('SELECT * FROM ' . $this->table_name . ' WHERE full_name = :full_name');

        $statement->execute(
            [
                ':full_name' => $full_name
            ]
        );

        $repository = $statement->fetch();
        $repository['private'] = (bool)$repository['private'];
        $repository['active'] = (bool)$repository['active'];
        return $repository;
    }

    /**
     * @param $owner
     * @return array
     */
    public function getAll($owner)
    {
        $statement = $this->client->prepare('SELECT * FROM ' . $this->table_name . ' WHERE owner = :owner');
        $statement->execute(
            [
                ':owner' => $owner
            ]
        );

        // convert booleans into booleans
        $repositories = $statement->fetchAll();
        foreach($repositories as &$repository) {
            $repository['private'] = (bool)$repository['private'];
            $repository['active'] = (bool)$repository['active'];
        }
        return $repositories;
    }

    /**
     * @param $full_name
     * @return bool
     */
    public function activate($full_name)
    {
        $statement = $this->client->prepare('UPDATE ' . $this->table_name . ' SET active = 1 WHERE full_name = :full_name');
        return $statement->execute(
            [
                ':full_name' => $full_name
            ]
        );
    }

    /**
     * @param $full_name
     * @throws UnavailableException
     * @return bool
     */
    public function deactivate($full_name)
    {
        $statement = $this->client->prepare('UPDATE ' . $this->table_name . ' SET active = 0 WHERE full_name = :full_name');
        return $statement->execute(
            [
                ':full_name' => $full_name
            ]
        );
    }

    /**
     * @param $full_name
     * @return bool
     */
    public function delete($full_name)
    {
        $statement = $this->client->prepare('DELETE FROM ' . $this->table_name . ' WHERE full_name = :full_name');
        return $statement->execute(
            [
                ':full_name' => $full_name
            ]
        );
    }
}
