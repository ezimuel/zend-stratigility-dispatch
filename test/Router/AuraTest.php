<?php

namespace ZendTest\Stratigility\Dispatch\Router;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stratigility\Dispatch\Router\Aura;

class AuraTest extends TestCase
{
    public function testConstructorWithEmptyRoutes()
    {
        $config = [ 'routes' => [] ];
        $aura = new Aura($config);
        $this->assertTrue($aura instanceof Aura);
    }

    public function testConstructoreWithRoutes()
    {
        $config = [ 'routes' => [
            'home' => [
                'url'    => '/',
                'action' => function () {
                    return true;
                }
            ]
        ]];
        $aura = new Aura($config);
        $this->assertTrue($aura instanceof Aura);
    }

    public function testMatch()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url'    => '/test',
                    'action' => function () {
                        return true;
                    }
                ]
            ]
        ];
        $aura = new Aura($config);

        $server = [
            'REQUEST_METHOD' => 'GET'
        ];

        $this->assertFalse($aura->match('/', $server));
        $this->assertTrue($aura->match('/test', $server));
        $this->assertFalse($aura->match('/test/foo', $server));
    }

    public function testMatchedParams()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url'    => '/test{/id}',
                    'action' => function () {
                        return true;
                    },
                    'tokens' => [
                       'id' => '(\d+)?'
                    ]
                ]
            ]
        ];
        $aura = new Aura($config);

        $server = [
            'REQUEST_METHOD' => 'GET'
        ];

        $this->assertTrue($aura->match('/test/12', $server));
        $params = $aura->getMatchedParams();
        $this->assertTrue(is_array($params));
        $this->assertTrue(isset($params['id']));
        $this->assertEquals(12, $params['id']);

        $this->assertTrue($aura->match('/test', $server));
        $params = $aura->getMatchedParams();
        $this->assertTrue(is_array($params));
        $this->assertFalse(isset($params['id']));
    }

    public function testMatchedRouteName()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url'    => '/test',
                    'action' => function () {
                        return true;
                    }
                ]
            ]
        ];
        $aura = new Aura($config);

        $server = [
            'REQUEST_METHOD' => 'GET'
        ];

        $this->assertTrue($aura->match('/test', $server));
        $this->assertEquals('home', $aura->getMatchedRouteName());
    }

    public function testMatchedAction()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url'    => '/test',
                    'action' => 'App/Test'
                ]
            ]
        ];
        $aura = new Aura($config);

        $server = [
            'REQUEST_METHOD' => 'GET'
        ];

        $this->assertTrue($aura->match('/test', $server));
        $this->assertEquals($config['routes']['home']['action'], $aura->getMatchedAction());

        $config = [
            'routes' => [
                'home' => [
                    'url'    => '/test',
                    'action' => function () {
                        return true;
                    }
                ]
            ]
        ];
        $aura = new Aura($config);

        $this->assertTrue($aura->match('/test', $server));
        $action = $aura->getMatchedAction();
        $this->assertTrue(is_callable($action));
        $this->assertTrue($action());
    }
}
