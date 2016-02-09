<?php namespace Test\Unit; 
use Ace\Repository\Store\RDBMSStoreFactory;
use PHPUnit_Framework_TestCase;

/**
 * @author timrodger
 * Date: 22/12/15
 */
class RDBMSStoreFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Ace\Repository\Store\UnavailableException
     */
    public function testCreateFailsForInvalidDsn()
    {
        $factory = new RDBMSStoreFactory(
            'invalid',
            'no-db',
            'user',
            'pass',
            'dir'
        );

        $factory->create();
    }
}
