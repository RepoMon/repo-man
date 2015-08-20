<?php namespace Sce\RepoMan\View;

use Symfony\Component\HttpFoundation\Request;
use Negotiation\FormatNegotiator;

/**
 * @author timrodger
 * Date: 22/07/15
 */
class ReportViewFactory
{
    /**
     * @param $name string type of view to create
     * @param $type string the content type required to be rendered by the view
     *
     * @return \Sce\RepoMan\View\ViewInterface
     */
    public function create($name, Request $request)
    {

        $priorities = $this->getAvailableContentTypes($name);

        $type = $this->getBestOutputType($priorities, $request);

        switch ($name) {
            case 'report/dependency':
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

            case 'report/consistency':
                switch ($type) {
                    case 'text/csv':
                    default:
                        return new ConsistencyReportCSVView();
                }
        }

        // return a default view? and error view? throw exception?
    }

    /**
     * @param $view_name
     * @param Request $request
     * @return string
     */
    public function getBestOutputType($priorities, Request $request)
    {
        $accept = $request->headers->get('Accept');

        $negotiator = new FormatNegotiator();
        $type = $negotiator->getBest($accept, $priorities);
        return $type ? $type->getValue() : $priorities[0];
    }

    /**
     * Return the supported content types for view $name
     * @param $name
     * @return array
     */
    private function getAvailableContentTypes($name)
    {
        switch ($name) {
            case 'report/dependency':
                return ['application/json', 'text/html', 'text/csv'];

            case 'report/consistency':
                return ['text/csv'];
        }

        return [];
    }
}