<?php namespace Sce;

/*
 * @author tim rodger
 * Date: 29/03/15
 */
class Configuration
{
    /**
     * @var string
     */
    private $base_dir;

    /**
     * @param $base_dir
     */
    public function __construct($base_dir)
    {
        $this->base_dir = $base_dir;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return $this->base_dir;
    }

    /**
     * @return array
     */
    public function getRepositories()
    {
        return [
        ];
    }
}