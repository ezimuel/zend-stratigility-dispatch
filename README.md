# zend-stratigility-dispatch

This component is a proposal for a middleware dispatcher compliant with [PSR-7](http://www.php-fig.org/psr/psr-7/)
standard.

For the routing part we used [Aura.Router](https://github.com/auraphp/Aura.Router)
library.

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
a simple configuration array in PHP, like the following:

```php
<?php
return [
    'route' => [
          // example of action class without dependencies
          'home' => [
              'url'    => '/',
              'action' => 'App\Home'
          ],
          // example of action class with dependencies
          'page' => [
              'url' => '/page',
              'action' => function($request, $response, $next) {
                  $db   = new PDO(...);
                  $page = new App\Page($db);
                  return $page($request, $response, $next);
              }
          ],
          // example of action with an optional parameter
          'search' => [
              'url'    => '/search{/query}',
              'tokens' => [ 'query' => '([^/]+)?' ],
              'action' => 'App\Search'
          ]
    ]
];
```

In order to use this component you can use the `MiddlewareDispatch::factory` to
create the dispatcher and attach it to a zend-stratigility application:

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

where the route configuration file is stored in `../config/route.php` file.

Note
====

This component is still in development, DO NOT use in production!
