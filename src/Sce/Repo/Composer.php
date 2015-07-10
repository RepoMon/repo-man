<?php namespace Sce\Repo;

/**
 * Represents a composer config contents & its lock file contents
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

    /**
     * @param array $config
     * @param array $lock
     */
    public function __construct(array $config, array $lock)
    {
        $this->config = $config;
        $this->lock = $lock;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasDependency($name)
    {
        $dependencies = $this->getDependencies();

        if (isset($dependencies[$name])){
            return true;
        }

        return false;
    }

    public function getDependencyVersion($name)
    {

    }

    public function getLockVersion($name)
    {

    }

    /**
     * Return a flat array of dependencies and versions
     * @return array
     */
    private function getDependencies()
    {
        $dependencies = [];

        if (isset($this->config['require'])){
            $dependencies = $this->config['require'];
        }

        return $dependencies;
    }
}