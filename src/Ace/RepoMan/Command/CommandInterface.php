<?php namespace Ace\RepoMan\Command;

/**
 * @author timrodger
 * Date: 25/07/15
 */
interface CommandInterface
{
    /**
     * Perform the action
     *
     * @param $data
     * @return mixed
     */
    public function execute($data);
}
