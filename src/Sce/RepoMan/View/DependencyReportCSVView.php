<?php namespace Sce\RepoMan\View;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class DependencyReportCSVView implements ViewInterface
{
    /**
     * @param $data
     */
    public function render($data)
    {
        $helper = new DependencyViewHelper();

        $lines = [];
        $headers = $helper->getHeader();
        array_unshift($headers, 'Vendor');
        $lines []= $headers;

        foreach($data as $name => $deps){

            list($vendor, $library) = explode('/', $name);

            foreach($deps as $version => $client_data){

                foreach($client_data as $client) {
                    $lines []=  [$vendor, $library, $version, $client['uri'].':'.$client['latest_tag'], $client['config_version'], $client['date']];
                }
            }
        }

        return (new CSVViewHelper())->generateCSV($lines);
    }

    public function getContentType()
    {
        return 'text/csv';
    }
}