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

           // var_dump($name);
           // var_dump($versions);
            if (count($versions) > 1) {
                $results = [];
            }
        }

        return $results;
    }
}