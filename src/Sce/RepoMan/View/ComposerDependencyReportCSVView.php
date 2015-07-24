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
        $helper = new ComposerDependencyViewHelper();
        $lines = $helper->formatDataAsLines($data);

        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        foreach ($lines as $row){
            fputcsv($csv, $row);
        }
        rewind($csv);
        $string = stream_get_contents($csv);
        fclose($csv);
        return trim($string);

    }
}