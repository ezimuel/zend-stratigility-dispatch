<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Stratigility\Dispatch;

use Aura\Router\Generator;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Router;

class MiddlewareDispatch
{
    public static function factory(array $config)
    {
        $routerClass = isset($config['router']) ? $config['router'] : 'Aura\Router\Route';
        $router = new Router(
            new RouteCollection(new RouteFactory($routerClass)),
            new Generator()
        );
        self::readConfig($router, $config);
        return new Dispatcher($router);
    }

    protected static function readConfig(Router $router, array $config)
    {
        if (!isset($config['route'])) {
            throw new Exception\InvalidArgumentException(
                sprintf("The route part is missing in the configuration")
            );
        }
        foreach ($config['route'] as $name => $data) {
            if (!isset($data['action'])) {
                throw new Exception\InvalidArgumentException(
                    sprintf("The action parameter is missing in route %s", $name)
                );
            }
            $router->add($name, $data['url']);
            if (!isset($data['values'])) {
                $data['values'] = [];
            }
            $data['values']['action'] = $data['action'];
            if (!isset($data['tokens'])) {
              $router->add($name, $data['url'])
                     ->addValues($data['values']);
            } else {
              $router->add($name, $data['url'])
                     ->addTokens($data['tokens'])
                     ->addValues($data['values']);
            }
        }
    }
}
