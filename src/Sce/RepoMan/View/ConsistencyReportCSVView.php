<?php namespace Sce\RepoMan\View;

/**
 * @author timrodger
 * Date: 20/08/15
 */
class ConsistencyReportCSVView implements ViewInterface
{

    public function render($data)
    {
        $lines = [];
        $lines []= ['Library', 'Repository', 'Configured', 'Actual'];

        foreach($data as $library => $repositories){
            foreach($repositories as $repository){
                $lines []= [$library, $repository['uri'], $repository['config_version'], $repository['actual']];
            }
        }

        return (new CSVViewHelper())->generateCSV($lines);

    }

    public function getContentType()
    {
        return 'text/csv';
    }
}