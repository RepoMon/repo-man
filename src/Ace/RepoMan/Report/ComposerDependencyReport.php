<?php namespace Ace\RepoMan\Report;

use Ace\RepoMan\Store\StoreInterface;
use Ace\RepoMan\Domain\ComposerConfig;

/**
 * @author timrodger
 * Date: 20/07/15
 */
class ComposerDependencyReport implements ReportInterface
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
     * @return array
     */
    public function generate()
    {
        $dependencies = [];

        // get the repositories
        foreach ($this->store->getAll() as $repository) {

            // if the repository is not checked out then do that now
            if (!$repository->isCheckedout()){
                $repository->update();
            }

            // create a composer instance for each repository
            $composer_json = $repository->getFile('composer.json');
            $composer_lock = $repository->getFile('composer.lock');

            $composer = new ComposerConfig(json_decode($composer_json, true), json_decode($composer_lock, true));

            $lock_dependencies = $composer->getLockDependencies();

            $repository_tag = $repository->getLatestTag();

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
                    'latest_tag' => $repository_tag,
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
