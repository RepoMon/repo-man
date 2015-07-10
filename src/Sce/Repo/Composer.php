<?php namespace Ace\Repo; 
/**
 * @author timrodger
 * Date: 10/07/15
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
}