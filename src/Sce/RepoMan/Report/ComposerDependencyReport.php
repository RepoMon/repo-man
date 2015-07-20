<?php namespace Sce\RepoMan\Report;

use Sce\RepoMan\Store\StoreInterface;

/**
 * @author timrodger
 * Date: 20/07/15
 */
class ComposerDependencyReport implements ReportInterface
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

    public function generate()
    {

    }
}