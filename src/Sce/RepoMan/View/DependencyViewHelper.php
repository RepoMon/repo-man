<?php namespace Sce\RepoMan\View;

/**
 * @package Sce\RepoMan\View
 */
class DependencyViewHelper
{
    /**
     * @param $data
     */
    public function formatDataAsLines($data)
    {
        $lines = [];
        $lines []= $this->getHeader();

        foreach($data as $name => $deps){

            $first_name = true;

            foreach($deps as $version => $client_data){

                $first_version = true;

                foreach($client_data as $client) {
                    if ($first_name) {
                        $lines []=  [$name, $version, $client['uri'].':'.$client['latest_tag'], $client['config_version'], $client['date']];
                        $first_name = false;
                        $first_version = false;
                    } elseif ($first_version){
                        $lines []=  ['', $version, $client['uri'].':'.$client['latest_tag'], $client['config_version'], $client['date']];
                        $first_version = false;
                    } else {
                        $lines []=  ['', '', $client['uri'].':'.$client['latest_tag'], $client['config_version'], $client['date']];
                    }
                }
            }

            $lines []=  ['','',''];
        }

        return $lines;

    }

    public function getHeader(){
        return ['Library', 'Version', 'Used By', 'Configured Version', 'Last Updated'];
    }
}