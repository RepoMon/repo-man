<?php

use Sce\Repo\Composer;

/**
 * @group unit
 * @author timrodger
 * Date: 10/07/15
 */
class ComposerUnitTest extends PHPUnit_Framework_TestCase
{

    public function testHasDependencyReturnsFalseWhenItsNotThere()
    {
        $config = [];
        $lock = [];
        $name = 'company/repo';
        $composer = new Composer($config, $lock);

        $result = $composer->hasDependency($name);

        $this->assertFalse($result);
    }

    public function testHasDependencyReturnsTrueWhenItsThere()
    {
        $name = 'company/repo';
        $config = ['require' => [$name => '1.0.0']];
        $lock = [];

        $composer = new Composer($config, $lock);

        $result = $composer->hasDependency($name);

        $this->assertTrue($result);
    }

    public function testHasDependencyReturnsTrueWhenItsADevDependency()
    {
        $name = 'company/repo';
        $config = ['require-dev' => [$name => '1.0.0']];
        $lock = [];

        $composer = new Composer($config, $lock);

        $result = $composer->hasDependency($name);

        $this->assertTrue($result);
    }

    public function testGetDependencyVersionReturnsNullWhenItsNotThere()
    {
        $config = [];
        $lock = [];
        $name = 'company/repo';
        $composer = new Composer($config, $lock);

        $result = $composer->getDependencyVersion($name);

        $this->assertNull($result);
    }

    public function testGetDependencyVersionReturnsVersionWhenItsThere()
    {
        $lock = [];
        $name = 'company/repo';
        $version = '1.0.0';
        $config = ['require' => [$name => $version]];
        $composer = new Composer($config, $lock);

        $result = $composer->getDependencyVersion($name);

        $this->assertSame($version, $result);
    }

    public function testGetDependencyVersionReturnsVersionWhenItsADevDependency()
    {
        $lock = [];
        $name = 'company/repo';
        $version = '1.0.0';
        $config = ['require-dev' => [$name => $version]];
        $composer = new Composer($config, $lock);

        $result = $composer->getDependencyVersion($name);

        $this->assertSame($version, $result);
    }

    public function testGetLockVersionReturnsNullWhenItsNotThere()
    {
        $config = [];
        $lock = [];
        $name = 'company/repo';
        $composer = new Composer($config, $lock);

        $result = $composer->getLockVersion($name);

        $this->assertNull($result);
    }

    public function testGetLockVersionReturnsVersionWhenItsThere()
    {
        $config = [];
        $name = 'company/repo';
        $version = '1.0.0';
        $lock = ["packages" => [
            ['name' => $name, 'version' => $version, 'time' => "2015-07-10 06:54:46"]
        ]];

        $composer = new Composer($config, $lock);

        $result = $composer->getLockVersion($name);

        $this->assertSame($version, $result);
    }

    public function testGetLockVersionReturnsVersionWhenItsADevDependency()
    {
        $config = [];
        $name = 'company/repo';
        $version = '1.0.0';
        $lock = ["packages-dev" => [
            ['name' => $name, 'version' => $version, 'time' => "2015-07-10 06:54:46"]
        ]];

        $composer = new Composer($config, $lock);

        $result = $composer->getLockVersion($name);

        $this->assertSame($version, $result);
    }

    public function testGetLockDateReturnsTheLockDate()
    {
        $config = [];
        $name = 'company/repo';
        $version = '1.0.0';
        $time = "2015-07-10 06:54:46";
        $lock = ["packages-dev" => [
            ['name' => $name, 'version' => $version, 'time' => $time]
        ]];

        $composer = new Composer($config, $lock);

        $result = $composer->getLockDate($name);

        $this->assertSame($time, $result);
    }
}