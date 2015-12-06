<?php namespace Ace\RepoMan\Report;

use Ace\RepoMan\Store\StoreInterface;

/**
 * @author timrodger
 * Date: 20/07/15
 */
class ReportFactory
{
    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * @param $name
     * @return \Ace\RepoMan\Report\ReportInterface
     */
    public function create($name)
    {
        switch ($name){

            case 'dependency/report':
                return new ComposerDependencyReport($this->store);

            default:
                return null;
        }
    }
}
