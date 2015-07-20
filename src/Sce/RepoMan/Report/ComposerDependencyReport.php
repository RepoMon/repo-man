<?php namespace Sce\RepoMan\Report;

use Sce\RepoMan\Store\StoreInterface;
use Sce\RepoMan\Domain\Composer;

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

    /**
     * @return array
     */
    public function generate()
    {
        $dependencies = [];

        // get the repositories
        foreach ($this->store->getAll() as $repository) {

            // create a composer instance for each repository
            $composer_json = $repository->getFile('composer.json');
            $composer_lock = $repository->getFile('composer.lock');

            $composer = new Composer(json_decode($composer_json, true), json_decode($composer_lock, true));

            $lock_dependencies = $composer->getLockDependencies();

            foreach($lock_dependencies as $name => $data){

                $version = $data['version'];

                if (!isset($dependencies[$name])){
                    $dependencies[$name] = [];
                }
                if (!isset($dependencies[$name][$version])){
                    $dependencies[$name][$version] = [];
                }

                // store the uri, configured version and date here
                $configured_version = $composer->getDependencyVersion($name);
                $date = $composer->getLockDate($name);

                $dependencies[$name][$version] []= [
                    'uri' => $repository->getUrl(),
                    'config_version' => $configured_version,
                    'date' => $date
                ];
            }

        }

        foreach($dependencies as $name => &$deps){
            ksort($deps, SORT_NATURAL);
        }

        return $dependencies;
    }
}