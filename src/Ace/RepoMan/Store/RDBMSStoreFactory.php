<?php namespace Ace\RepoMan\Store;

use PDO;
use PDOException;

/**
 * @author timrodger
 * Date: 10/12/15
 */
class RDBMSStoreFactory implements StoreFactoryInterface
{
    /**
     * @var string
     */
    private $db_host;

    /**
     * @var string
     */
    private $db_name;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $table_name = 'repositories';

    /**
     * @var string
     */
    private $directory;

    /**
     * @param string $db_host
     * @param string $db_name
     * @param string $user
     * @param string $password
     * @param string $directory
     */
    public function __construct($db_host, $db_name, $user, $password, $directory)
    {
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->user = $user;
        $this->password = $password;
        $this->directory = $directory;
    }

    /**
     * @return RDBMSStore
     * @throws UnavailableException
     */
    public function create()
    {
        try {
            $dsn = sprintf('mysql:host=%s', $this->db_host);
            $pdo = new PDO($dsn, $this->user, $this->password);

            // ensure db exists
            $pdo->query(sprintf('CREATE DATABASE IF NOT EXISTS %s', $this->db_name));
            $pdo->query(sprintf('use %s', $this->db_name));

            // ensure table exists
            $pdo->query(
                sprintf('CREATE TABLE IF NOT EXISTS %s (
                  url VARCHAR (2048) UNIQUE NOT NULL,
                  full_name VARCHAR (2048) UNIQUE NOT NULL,
                  description TEXT,
                  owner TEXT NOT NULL,
                  lang TEXT,
                  version TEXT DEFAULT "",
                  dependency_manager TEXT,
                  branch TEXT,
                  timezone TEXT,
                  active INT NOT NULL DEFAULT 0,
                  private INT NOT NULL DEFAULT 0
                )', $this->table_name)
            );

            return new RDBMSStore($pdo, $this->table_name, $this->directory);
        } catch (PDOException $ex) {
            throw new UnavailableException($ex->getMessage());
        }
    }
}