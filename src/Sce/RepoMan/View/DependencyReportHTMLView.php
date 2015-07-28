<?php namespace Sce\RepoMan\View;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class DependencyReportHTMLView implements ViewInterface
{
    /**
     * @param $data
     */
    public function render($data)
    {
        $helper = new DependencyViewHelper();
        $lines = $helper->formatDataAsLines($data);
        $headers = array_shift($lines);

        // generate a html table from lines
        $html = "<table>\n";

        $html .= "<thead><tr>";
        foreach($headers as $header) {
            $html .= "<th>" . $header . "</th>";
        }
        $html .= "</tr></thead>\n";

        foreach ($lines as $line){
            $html .= "<tr>";
            foreach($line as $cell) {
                $html .= "<td>" . $cell . "</td>";
            }
            $html .= "/<tr>\n";
        }

        $html .= "</table>";

        return $html;
    }

}