<?php namespace Sce\RepoMan\View;

/**
 * @author timrodger
 * Date: 22/07/15
 */
interface ViewInterface
{
    /**
     * @param $data
     * @return string
     */
    public function render($data);

    /**
     * @return string
     */
    public function getContentType();
}