<?php namespace Sce\RepoMan\View;

/**
 * @author timrodger
 * Date: 20/08/15
 */
class CSVViewHelper
{

    /**
     * Converts an array into csv
     * @param array $lines
     * @return string
     */
    public function generateCSV(array $lines)
    {
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