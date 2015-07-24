<?php namespace Sce\RepoMan\View;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class ComposerDependencyReportCSVView implements ViewInterface
{
    /**
     * @param $data
     */
    public function render($data)
    {
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

        //$lines = [];
        // $lines []= $this->getHeader();
        fputcsv($csv, $this->getHeader());

        foreach($data as $name => $deps){

            $first_name = true;

            foreach($deps as $version => $client_data){

                $first_version = true;

                foreach($client_data as $client) {
                    if ($first_name) {
                        fputcsv($csv, [$name, $version, $client['uri'].':'.$client['latest_tag'], $client['config_version'], $client['date']]);
                        $first_name = false;
                        $first_version = false;
                    } elseif ($first_version){
                        fputcsv($csv, ['', $version, $client['uri'].':'.$client['latest_tag'], $client['config_version'], $client['date']]);
                        $first_version = false;
                    } else {
                        fputcsv($csv, ['', '', $client['uri'].':'.$client['latest_tag'], $client['config_version'], $client['date']]);
                    }
                }
            }

            fputcsv($csv, ['','','']);
        }


//        foreach ($lines as $row){
//            fputcsv($csv, $row);
//        }
        rewind($csv);
        $string = stream_get_contents($csv);
        fclose($csv);
        return trim($string);

    }

    private function getHeader(){
        return ['Library', 'Version', 'Used By', 'Configured Version', 'Last Updated'];
    }
}