<?php namespace Sce\RepoMan\View;

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
     * @return \Sce\RepoMan\View\ViewInterface
     */
    public function create($name, $type)
    {
        switch ($name) {
            case 'dependency/composer':

        }
    }
}