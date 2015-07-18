<?php namespace Sce\RepoMan\Git;

use Sce\RepoMan\Configuration;
use Predis\Client;

/**
 * Uses redis to store data on git repositories
 * The contents of the repositories are on the file system
 *
 * @author timrodger
 * Date: 17/07/15
 */
class Store implements StoreInterface
{
    /**
     * the key used to store the set of git repository urls being managed
     */
    const REPO_SET_NAME = 'git-repositories';

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array of GitRepo instances
     */
    private $repositories = [];

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * @return array of GitRepo instances
     */
    public function getAll()
    {
        if (count($this->repositories)){
            return $this->repositories;
        }

        // get the repository data from redis
        $keys = $this->client->smembers(SELF::REPO_SET_NAME);

        if (is_array($keys)) {
            foreach ($keys as $key) {
                $data = $this->client->hmget($key, 'url', 'path');
                $this->repositories [] = new Repository($data[0], $data[1]);
            }
        }

        return $this->repositories;
    }
}