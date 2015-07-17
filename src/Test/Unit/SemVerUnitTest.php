<?php

use Sce\Repo\SemVer;

/**
 * @group unit
 * @author timrodger
 * Date: 12/07/15
 */
class SemVerUnitTest extends PHPUnit_Framework_TestCase
{

    public function getVersionData()
    {
        return [
            ['v1.0.0', 1, 0, 0],
            ['0.7.3', 0, 7, 3],
            ['0.0.0 ', 0, 0, 0],
            ['3.1.4a ', 3, 1, 4],
        ];
    }

    /**
     * @dataProvider getVersionData
     * @param $value
     * @param $major
     * @param $minor
     * @param $patch
     */
    public function testGetMajorVersion($value, $major, $minor, $patch)
    {
        $sem_ver = new SemVer($value);
        $this->assertSame($major, $sem_ver->getMajorVersion());
    }

    /**
     * @dataProvider getVersionData
     * @param $value
     * @param $major
     * @param $minor
     * @param $patch
     */
    public function testGetMinorVersion($value, $major, $minor, $patch)
    {
        $sem_ver = new SemVer($value);
        $this->assertSame($minor, $sem_ver->getMinorVersion());
    }

    /**
     * @dataProvider getVersionData
     * @param $value
     * @param $major
     * @param $minor
     * @param $patch
     */
    public function testGetPatchVersion($value, $major, $minor, $patch)
    {
        $sem_ver = new SemVer($value);
        $this->assertSame($patch, $sem_ver->getPatchVersion());
    }

    public function getInvalidVersionValues()
    {
        return [
            ['not a semantic version string'],
            ['v1.6a.4']
        ];
    }

    /**
     * @dataProvider getInvalidVersionValues
     * @param $invalid
     */
    public function testSemVerHandleInvalidInput($invalid)
    {
        $sem_ver = new SemVer($invalid);
        $this->assertFalse($sem_ver->isValid());
    }

    /**
     * @dataProvider getInvalidVersionValues
     * @param $invalid
     */
    public function testGetMajorVersionReturnsNullForInvalidInput($invalid)
    {
        $sem_ver = new SemVer($invalid);
        $this->assertNull($sem_ver->getMajorVersion());
    }

    /**
     * @dataProvider getInvalidVersionValues
     * @param $invalid
     */
    public function testGetMinorVersionReturnsNullForInvalidInput($invalid)
    {
        $sem_ver = new SemVer($invalid);
        $this->assertNull($sem_ver->getMinorVersion());
    }

    /**
     * @dataProvider getInvalidVersionValues
     * @param $invalid
     */
    public function testGetPatchVersionReturnsNullForInvalidInput($invalid)
    {
        $sem_ver = new SemVer($invalid);
        $this->assertNull($sem_ver->getPatchVersion());
    }

    /**
     * @dataProvider getVersionData
     * @param $value
     */
    public function testToStringReturnsOriginalString($value)
    {
        $sem_ver = new SemVer($value);
        $this->assertSame($value, (string)$sem_ver);
    }
}