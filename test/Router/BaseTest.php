<?php

namespace ZendTest\Stratigility\Dispatch\Router;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stratigility\Dispatch\Router\Aura;
use Zend\Stratigility\Dispatch\Router\FastRoute;

class BaseTest extends TestCase
{

    public function getAdapters()
    {
        $adapters = [];
        if (class_exists('Aura\Router\Router')) {
            $adapters[] = [ 'Zend\Stratigility\Dispatch\Router\Aura' ];
        }
        if (class_exists('FastRoute\RouteCollector')) {
            $adapters[] = [ 'Zend\Stratigility\Dispatch\Router\FastRoute' ];
        }
        return $adapters;
    }

    /**
     * @dataProvider getAdapters
     */
    public function testConstructor($adapter)
    {
        $router = new $adapter();
        $this->assertTrue($router instanceof $adapter);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testSetConfig($adapter)
    {
        $config = [ 'routes' => [
            'home' => [
                'url'    => '/',
                'action' => function () {
                    return true;
                }
            ]
        ]];
        $router = new $adapter();
        $router->setConfig($config);
        $this->assertEquals($config, $router->getConfig());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMatch($adapter)
    {
        $config = [
            'routes' => [
                'home' => [
                    'url'    => '/test',
                    'methods' => ['GET'],
                    'action' => function () {
                        return true;
                    }
                ]
            ]
        ];
        $router = new $adapter();
        $router->setConfig($config);

        $server = [
            'REQUEST_METHOD' => 'GET'
        ];

        $this->assertFalse($router->match('/', $server));
        $this->assertTrue($router->match('/test', $server));
        $this->assertFalse($router->match('/test/foo', $server));
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMatchedRouteName($adapter)
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
        $router = new $adapter();
        $router->setConfig($config);

        $server = [
            'REQUEST_METHOD' => 'GET'
        ];

        $this->assertTrue($router->match('/test', $server));
        $this->assertEquals('home', $router->getMatchedRouteName());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMatchedAction($adapter)
    {
        $config = [
            'routes' => [
                'home' => [
                    'url'    => '/test',
                    'action' => 'App/Test'
                ]
            ]
        ];
        $router = new $adapter();
        $router->setConfig($config);

        $server = [
            'REQUEST_METHOD' => 'GET'
        ];

        $this->assertTrue($router->match('/test', $server));
        $this->assertEquals($config['routes']['home']['action'], $router->getMatchedAction());

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
        $router = new $adapter();
        $router->setConfig($config);

        $this->assertTrue($router->match('/test', $server));
        $action = $router->getMatchedAction();
        $this->assertTrue(is_callable($action));
        $this->assertTrue($action());
    }
}
