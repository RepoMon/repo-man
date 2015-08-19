<?php namespace Sce\RepoMan\Report; 

/**
 * @author timrodger
 * Date: 19/08/15
 */
class DependencyConsistencyReport extends ComposerDependencyReport
{

    public function generate()
    {
        $results = null;

        $data = parent::generate();

        foreach($data as $name => $versions){

            if (count($versions) > 1) {
                $results = [];

                $results[$name] = [];

                foreach($versions as $version => $data){
                    foreach($data as $item){
                        $results[$name] []= ['uri' => $item['uri'], 'requested' => $item['config_version'], 'actual' => $version];
                    }
                }
            }
        }

        return $results;
    }
}