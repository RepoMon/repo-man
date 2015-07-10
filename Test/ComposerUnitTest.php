<?php

use Sce\Repo\Composer;

/**
 * @author timrodger
 * Date: 10/07/15
 */
class ComposerUnitTest extends PHPUnit_Framework_TestCase
{

    public function testHasDependency()
    {
        $config = [];
        $lock = [];
        $name = 'company/repo';
        $composer = new Composer($config, $lock);

        $result = $composer->hasDependency($name);

        $this->assertFalse($result);
    }
}