<?php

namespace ZendTest\Stratigility\Dispatch;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stratigility\Dispatch\MiddlewareDispatch;
use Zend\Stratigility\Dispatch\Dispatcher;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;
use ZendTest\Stratigility\Dispatch\TestAsset\Container;
use ZendTest\Stratigility\Dispatch\TestAsset\Test;

class MiddlewareDispatchTest extends TestCase
{
    public function setUp()
    {
        $this->config = require 'TestAsset/config.php';
    }

    public function testFactory()
    {
        $dispatcher = MiddlewareDispatch::factory($this->config);

        $this->assertTrue($dispatcher instanceof Dispatcher);
    }

    public function testRoutingWithClassName()
    {
        $dispatcher = MiddlewareDispatch::factory($this->config);

        $request  = new ServerRequest();
        $request  = $request->withUri(new Uri('/'));
        $response = new Response();

        $this->assertTrue($dispatcher($request, $response, function () {
        }));
    }

    public function testRoutingWithCallable()
    {
        $dispatcher = MiddlewareDispatch::factory($this->config);

        $request  = new ServerRequest();
        $request  = $request->withUri(new Uri('/page'));
        $response = new Response();

        $this->assertTrue($dispatcher($request, $response, function () {
        }));
    }

    public function testRoutingWithClassNameAndParams()
    {
        $dispatcher = MiddlewareDispatch::factory($this->config);

        $request  = new ServerRequest();
        $request  = $request->withUri(new Uri('/search'));
        $response = new Response();

        $this->assertTrue($dispatcher($request, $response, function () {
        }));

        $request  = $request->withUri(new Uri('/search/test'));
        $this->assertEquals('test', $dispatcher($request, $response, function () {
        }));
    }

    public function testRoutingWithContainer()
    {
        $container = new Container();
        $container->set('ObjectFromContainer', new Test());

        $dispatcher = MiddlewareDispatch::factory($this->config);
        $dispatcher->setContainer($container);

        $request  = new ServerRequest();
        $request  = $request->withUri(new Uri('/test'));
        $response = new Response();

        $this->assertTrue($dispatcher($request, $response, function () {
        }));
    }
}
