<?php namespace Sce\RepoMan\Store;

use Predis\Response\ServerException;
use Sce\RepoMan\Configuration;
use Predis\Client;
use Sce\RepoMan\Store\UnavailableException;
use Sce\RepoMan\Domain\Repository;

/**
 * Uses redis to store data on git repositories
 * The contents of the repositories are on the file system
 *
 * @author timrodger
 * Date: 17/07/15
 */
class Redis implements StoreInterface
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

        $keys = $this->getRepositories();

         // get the repository data from redis
        if (is_array($keys)) {
            foreach ($keys as $key) {
                // insert token into the repo url here
                $this->repositories [] = $this->createRepository($key);
            }
        }
        return $this->repositories;
    }

    /**
     * @return array
     * @throws UnavailableException
     */
    private function getRepositories()
    {
        try {
            // get the repository data from redis
            return $this->client->smembers(SELF::REPO_SET_NAME);
        } catch (ServerException $ex) {
            throw new UnavailableException($ex->getMessage());
        }
    }

    /**
     * @param $url
     * @return Repository
     * @throws UnavailableException
     */
    public function get($url)
    {
        $keys = $this->getRepositories();

        if (is_array($keys) && in_array($url, $keys)){
            return $this->createRepository($url);
        } else {
            throw new UnavailableException("Can't get '$url'");
        }
    }

    /**
     * @param $url
     */
    public function add($url)
    {
        try {
            // insert token into the repo url here
            $repository = $this->createRepository($url);

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
            $this->client->set($this->getTokenKey($host), $token);
        } catch (ServerException $ex) {
            throw new UnavailableException($ex->getMessage());
        }
    }

    /**
     * @param $host
     * @return string
     */
    public function getToken($host)
    {
        try {
            return $this->client->get($this->getTokenKey($host));
        } catch (ServerException $ex) {
            throw new UnavailableException($ex->getMessage());
        }
    }

    /**
     * Create a new Repository instance, pass it the token to use if one is available
     *
     * @param $url string
     */
    private function createRepository($url)
    {
        // try to get the token for this url
        $parts = parse_url($url);

        if (isset($parts['token'])) {
            $token = $this->client->get($this->getTokenKey($parts['host']));
        } else {
            // or throw an exception?
            $token = '';
        }

        return new Repository($url, $this->config->getRepoDir(), $token);
    }

    /**
     * Generate the key to store the token for this host
     *
     * @param $host
     * @return string
     */
    private function getTokenKey($host)
    {
        return self::TOKEN_SET_NAME . ':' . $host;
    }
 }