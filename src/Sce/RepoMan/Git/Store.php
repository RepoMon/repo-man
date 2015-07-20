<?php namespace Sce\RepoMan\Git;

use Predis\Response\ServerException;
use Sce\RepoMan\Configuration;
use Predis\Client;
use Sce\RepoMan\Git\UnavailableException;

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
     * key used to store the host names that have tokens attached
     */
    const TOKEN_SET_NAME = 'git-host-tokens';

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

        try {
            // get the repository data from redis
            $keys = $this->client->smembers(SELF::REPO_SET_NAME);
            if (is_array($keys)) {
                foreach ($keys as $key) {
                    // insert token into the repo url here
                    $this->repositories [] = new Repository($key, $this->config->getRepoDir());
                }
            }
            return $this->repositories;

        } catch (ServerException $ex) {
            throw new UnavailableException($ex->getMessage());
        }
    }

    /**
     * @param $url
     */
    public function add($url)
    {
        try {
            // insert token into the repo url here
            $repository = new Repository($url, $this->config->getRepoDir());

            // add to set in redis
            $this->client->sadd(self::REPO_SET_NAME, $url);

            return $repository;

        } catch (ServerException $ex) {
            throw new UnavailableException($ex->getMessage());
        }
    }

    /**
     * @param $host
     * @param $token
     */
    public function addToken($host, $token)
    {
        try {
            $this->client->set(self::TOKEN_SET_NAME . ':' . $host, $token);
        } catch (ServerException $ex) {
            throw new UnavailableException($ex->getMessage());
        }
    }
 }