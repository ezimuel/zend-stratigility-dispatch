# zend-stratigility-dispatch

[![Build Status](https://travis-ci.org/ezimuel/zend-stratigility-dispatch.svg?branch=master)](https://travis-ci.org/ezimuel/zend-stratigility-dispatch)

This component is a proposal for a PHP middleware dispatcher compliant with [PSR-7](http://www.php-fig.org/psr/psr-7/)
standard. This components has been developed with [zend-stratigility](https://github.com/zendframework/zend-stratigility)
in mind, but can be used in any PSR-7 middleware application.

For the routing part we used a pluggable adapter architecture using a simple [RouterInterface](https://github.com/ezimuel/zend-stratigility-dispatch/tree/master/src/Router/RouterInterface.php)

We provide a default router adapter using the [Aura.Router](https://github.com/auraphp/Aura.Router) library.

You can write your adapter implementing the `Zend\Stratigility\Dispatch\Router\RouterInterface`.

Installation
============

You can install this component in your PHP application using composer. You need
to add the following require in your composer.json file.

```
"ezimuel/zend-stratigility-dispatch" : "dev-master"
```

Basic usage
===========

The main goal of this component is to dispatch a PHP middleware application using
a simple configuration array, like the following:

```php
<?php
return [
    'router' => [
        'adapter' => 'Zend\Stratigility\Dispatch\Router\Aura'
    ],
    'routes' => [
        // example of action class without dependencies
        'home' => [
            'url'    => '/',
            'action' => 'App/Home'
        ],
        // example of action class with dependencies using a factory
        'page' => [
            'url' => '/page',
            'action' => function($request, $response, $next) {
                $bar  = new ZendTest\Stratigility\Dispatch\TestAsset\Bar();
                $page = new ZendTest\Stratigility\Dispatch\TestAsset\Page($bar);
                return $page->action($request, $response, $next);
            }
        ],
        // example of action class using optional parameter in the URL
        // the syntax of the URL and the tokens depend on the router adapter (Aura in this case)
        'search' => [
            'url'    => '/search{/query}',
            'tokens' => [ 'query' => '([^/]+)?' ],
            'action' => 'App/Search'
        ]
    ]
];
```
The `router` key specifies the routing adapter to use, the default is the Aura.Router.
All the routes are specified in the `routes` array. For each route you need to specify
at least a `url` and an `action` that can be a callable, a class name, a static function
name, etc.

To use the dispatcher you can call the `MiddlewareDispatch::factory` that create
an instance of Dispatcher. With this istance in place you can just attach it to a
[zend-stratigility](https://github.com/zendframework/zend-stratigility) application
using the `MiddlewarePipe` component:

```php
use Zend\Stratigility\MiddlewarePipe;
use Zend\Diactoros\Server;
use Zend\Stratigility\Dispatch\MiddlewareDispatch;

require '../vendor/autoload.php';

$app = new MiddlewarePipe();
$app->pipe('/', MiddlewareDispatch::factory(require '../config/route.php'));

$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$server->listen();
```

where the previous configuration file is stored in `../config/route.php` file.

Using a Container
=================

If you want you can consume actions from a DI Container. We used the `ContainerInterface`
of the [Container Interoperability](https://github.com/container-interop/container-interop)
project. This is a proposal for a [PSR](http://www.php-fig.org/) standard.

To use a Container you need to inject in the dispatcher using the `setContainer()` method.

Here is reported an example:

```php

// here the code to create a $container (implementing the Interop\Container\ContainerInterface)

$app = new MiddlewarePipe();

$dispatcher = MiddlewareDispatch::factory(require '../config/route.php');
$dispatcher->setContainer($container);

$app->pipe('/', $dispatcher);

$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$server->listen();
```

Note
====

This component is still in development, DO NOT use in production!
