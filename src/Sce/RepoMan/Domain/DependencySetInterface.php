<?php namespace Sce\RepoMan\Domain; 
/**
 * @author timrodger
 * Date: 29/07/15
 */
interface DependencySetInterface
{
    public function setRequiredVersions(array $versions);

}