<?php

namespace ZendTest\Stratigility\Dispatch;

use PHPUnit_Framework_TestCase as TestCase;
use Aura\Router\Generator;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Router;
use Zend\Stratigility\Dispatch\Dispatcher;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;

class DispatcherTest extends TestCase
{
    public function setUp()
    {
        $this->router = new Router(
            new RouteCollection(new RouteFactory()),
            new Generator()
        );

        $this->request  = new ServerRequest();
        $this->response = new Response();
    }

    public function testDispatchInvokableClass()
    {
        $this->router->add('home', '/')
                     ->addValues([ 'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Home' ]);

        $dispatch = new Dispatcher($this->router);
        $this->request = $this->request->withUri(new Uri('/'));
        $result = $dispatch($this->request, $this->response, function(){});

        $this->assertTrue($result);
    }

    /**
     *  @expectedException  Zend\Stratigility\Dispatch\Exception\InvalidArgumentException
     */
    public function testDispatchNotInvokableClass()
    {
        $this->router->add('home', '/error')
                     ->addValues([ 'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Bar' ]);

        $dispatch = new Dispatcher($this->router);
        $this->request = $this->request->withUri(new Uri('/error'));
        $result = $dispatch($this->request, $this->response, function(){});
    }

    public function testDispatchCallable()
    {
        $this->router->add('page', '/page')
                     ->addValues([
                       'action' => function($request, $response, $next){
                          return true;
                       }]);

        $dispatch = new Dispatcher($this->router);
        $this->request = $this->request->withUri(new Uri('/page'));
        $result = $dispatch($this->request, $this->response, function(){});

        $this->assertTrue($result);
    }

    public function testDispatchCallableStringClassMethodAction()
    {
        $this->router->add('myclass', '/')
                     ->addValues([ 'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\ClassMethod::myMethod' ]);

        $dispatch = new Dispatcher($this->router);
        $this->request = $this->request->withUri(new Uri('/'));
        $result = $dispatch($this->request, $this->response, function(){});

        $this->assertTrue($result);
    }
}
