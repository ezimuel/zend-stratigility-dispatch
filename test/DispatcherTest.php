<?php

namespace ZendTest\Stratigility\Dispatch;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stratigility\Dispatch\Router\Aura;
use Zend\Stratigility\Dispatch\Dispatcher;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;
use ZendTest\Stratigility\Dispatch\TestAsset\Container;

class DispatcherTest extends TestCase
{
    public function setUp()
    {
        $this->router   = new Aura();
        $this->response = new Response();
    }

    public function testConstruct()
    {
        $this->router->setConfig([ 'routes' => [] ]);
        $dispatch = new Dispatcher($this->router);
        $this->assertTrue($dispatch instanceof Dispatcher);
    }

    public function testConstructWithContainer()
    {
        $container = new Container();
        $this->router->setConfig([ 'routes' => [] ]);
        $dispatch  = new Dispatcher($this->router, $container);
        $this->assertTrue($dispatch instanceof Dispatcher);
    }

    public function testSetRouter()
    {
        $dispatch = new Dispatcher($this->router);
        $config = [
            'routes' => [
                'home' => [
                    'url' => '/',
                    'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Home'
                ]
            ]
        ];
        $this->router->setConfig($config);
        $dispatch->setRouter($this->router);
        $this->assertEquals($this->router, $dispatch->getRouter());
    }

    public function testSetContainer()
    {
        $container = new Container();
        $dispatch  = new Dispatcher($this->router);

        $dispatch->setContainer($container);
        $this->assertEquals($container, $dispatch->getContainer());
    }

    public function testDispatchInvokableClass()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url' => '/',
                    'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Home'
                ]
            ]
        ];
        $this->router->setConfig($config);
        $dispatch       = new Dispatcher($this->router);
        $this->request  = new ServerRequest(['REQUEST_METHOD' => 'GET'], [], $config['routes']['home']['url']);

        $result = $dispatch($this->request, $this->response, function(){});
        $this->assertTrue($result);
    }

    /**
     *  @expectedException  Zend\Stratigility\Dispatch\Exception\InvalidArgumentException
     */
    public function testDispatchNotInvokableClass()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url' => '/error',
                    'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Bar'
                ]
            ]
        ];
        $this->router->setConfig($config);
        $dispatch       = new Dispatcher($this->router);
        $this->request  = new ServerRequest(['REQUEST_METHOD' => 'GET'], [], $config['routes']['home']['url']);

        $result = $dispatch($this->request, $this->response, function(){});
    }

    public function testDispatchCallable()
    {
        $config = [
            'routes' => [
                'page' => [
                    'url' => '/page',
                    'action' => function ($request, $response, $next) {
                        return true;
                    }
                ]
            ]
        ];
        $this->router->setConfig($config);
        $dispatch       = new Dispatcher($this->router);
        $this->request  = new ServerRequest(['REQUEST_METHOD' => 'GET'], [], $config['routes']['page']['url']);

        $result = $dispatch($this->request, $this->response, function(){});
        $this->assertTrue($result);
    }

    public function testDispatchCallableStringClassMethodAction()
    {
        $config = [
            'routes' => [
                'myclass' => [
                    'url' => '/',
                    'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\ClassMethod::myMethod'
                ]
            ]
        ];
        $this->router->setConfig($config);
        $dispatch       = new Dispatcher($this->router);
        $this->request  = new ServerRequest(['REQUEST_METHOD' => 'GET'], [], $config['routes']['myclass']['url']);

        $result = $dispatch($this->request, $this->response, function(){});
        $this->assertTrue($result);
    }
}
