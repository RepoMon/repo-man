<?php namespace Sce\Repo;

/**
 * Provides access to the elements of a semantic version string
 */
class SemVer
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var array
     */
    private $versions;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;

        preg_match('/(\d+)\.(\d+)\.(\d+)/', $value, $matches);

        if (count($matches) === 4) {
            $this->versions ['major'] = (integer)$matches[1];
            $this->versions ['minor'] = (integer)$matches[2];
            $this->versions ['patch'] = (integer)$matches[3];
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return is_array($this->versions);
    }

    /**
     * @return mixed
     */
    public function getMajorVersion()
    {
        return $this->versions['major'];
    }

    /**
     * @return mixed
     */
    public function getMinorVersion()
    {
        return $this->versions['minor'];
    }

    /**
     * @return mixed
     */
    public function getPatchVersion()
    {
        return $this->versions['patch'];
    }

    public function __toString()
    {
        return $this->value;
    }
}