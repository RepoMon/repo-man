<?php namespace Ace\RepoMan\View;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class ViewFactory
{
    /**
     * @param $name string type of view to create
     * @param $type string the content type required to be rendered by the view
     *
     * @return \Ace\RepoMan\View\ViewInterface
     */
    public function create($name, $type)
    {
        switch ($name) {
            case 'dependency/report':
                switch ($type) {
                    case 'text/csv':
                        return new DependencyReportCSVView();
                    case 'application/json':
                        return new DependencyReportJSONView();
                    case 'text/html':
                        return new DependencyReportHTMLView();
                    default:
                        return new DependencyReportJSONView();
                }

        }
    }

    /**
     * Return the supported content types for view $name
     * @param $name
     * @return array
     */
    public function getAvailableContentTypes($name)
    {
        return ['application/json', 'text/html', 'text/csv'];
    }
}
