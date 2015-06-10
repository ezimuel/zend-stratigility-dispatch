<?php

namespace ZendTest\Stratigility\Dispatch\Router;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stratigility\Dispatch\Router\Aura;

class AuraTest extends TestCase
{
    public function testConstructor()
    {
        $config = [
            'routes' => [ ]
        ];
        $aura = new Aura($config);
        $this->assertTrue($aura instanceof Aura);
    }
}
