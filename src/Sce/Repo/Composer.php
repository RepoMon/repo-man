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

    /**
     * @param $name
     * @return mixed
     */
    public function getDependencyVersion($name)
    {
        if ($this->hasDependency($name)){
            $dependencies = $this->getDependencies();
            return $dependencies[$name];
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getLockVersion($name)
    {
        $lock_dependencies = $this->getLockDependencies();
        if (isset($lock_dependencies[$name])){
            return $lock_dependencies[$name];
        }
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

        if (isset($this->config['require-dev'])){
            $dependencies = array_merge($dependencies, $this->config['require-dev']);
        }

        return $dependencies;
    }

    /**
     * @return array
     */
    private function getLockDependencies()
    {
        $dependencies = [];

        if (isset($this->lock['packages'])) {
            foreach ($this->lock['packages'] as $package) {
                $dependencies[$package['name']] = $package['version'];
            }
        }

        if (isset($this->lock['packages-dev'])) {
            foreach ($this->lock['packages-dev'] as $package) {
                $dependencies[$package['name']] = $package['version'];
            }
        }

        return $dependencies;
    }
}