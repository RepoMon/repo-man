<?php namespace Sce\Repo;

/**
 * Represents a composer config file & lock
 */
class Composer
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $lock;

    public function __construct(array $config, array $lock)
    {
        $this->config = $config;
        $this->lock = $lock;
    }

    public function hasDependency($name)
    {

    }

    public function getDependencyVersion($name)
    {

    }

    public function getLockVersion($name)
    {

    }
}