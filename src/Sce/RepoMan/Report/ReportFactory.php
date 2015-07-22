<?php namespace Sce\RepoMan\Report;

use Sce\RepoMan\Store\StoreInterface;

/**
 * @author timrodger
 * Date: 20/07/15
 */
class ReportFactory
{
    /**
     * @var \Sce\Repoman\Store\StoreInterface
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
     * @return \Sce\RepoMan\Report\ReportInterface
     */
    public function create($name)
    {
        switch ($name){

            case 'dependency/composer':
                return new ComposerDependencyReport($this->store);

            default:
                return null;
        }
    }
}