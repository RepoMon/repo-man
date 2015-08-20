<?php namespace Sce\RepoMan\View;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class DependencyReportJSONView implements ViewInterface
{
    /**
     * @param $data
     */
    public function render($data)
    {
        return json_encode($data);
    }


    public function getContentType()
    {
        return 'application/json';
    }
}