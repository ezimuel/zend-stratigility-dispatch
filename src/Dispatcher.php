<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Stratigility\Dispatch;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Router\Router;

class Dispatcher
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->setRouter($router);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $path  = $request->getUri()->getPath();
        $route = $this->router->match($path, $request->getServerParams());
        if (!$route) {
            return $next($request, $response);
        }
        foreach ($route->params as $param => $value) {
            $request = $request->withAttribute($param, $value);
        }
        if (!isset($route->params['action'])) {
            throw new Exception\InvalidArgumentException(
                sprintf("The route %s doesn't have an action to dispatch", $route->name)
            );
        }
        if (is_callable($route->params['action'])) {
            return $route->params['action']($request, $response, $next);
        } elseif (is_string($route->params['action'])) {
            $action = new $route->params['action'];
            if (is_callable($action)) {
              return $action($request, $response, $next);
            }
        }
        throw new Exception\InvalidArgumentException(
            sprintf("The action class specified %s is not invokable", $route->params['action'])
        );
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function getRouter()
    {
        return $this->router;
    }
}
