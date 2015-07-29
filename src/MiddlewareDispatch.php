<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Stratigility\Dispatch;

class MiddlewareDispatch
{
    const DEFAULT_ROUTER = 'Zend\Stratigility\Dispatch\Router\Aura';

    /**
     * Create new Dispatcher instance
     *
     * @param array $config
     */
    public static function factory(array $config)
    {
        self::checkConfig($config);
        $adapter = isset($config['router']['adapter']) ?
                   $config['router']['adapter'] :
                   self::DEFAULT_ROUTER;
        $router  = new $adapter();
        $router->setConfig($config);
        return new Dispatcher($router);
    }

    /**
     * Check configuration
     *
     * @param  array $config
     * @throws Exception\InvalidArgumentException
     */
    protected static function checkConfig(array $config)
    {
        if (isset($config['router']['adapter'])) {
            if (!class_exists($config['router']['adapter'])) {
                throw new Exception\InvalidArgumentException(
                    sprintf("The router specified %s doesn't exist", $config['router'])
                );
            }
        }
        if (!isset($config['routes'])) {
            throw new Exception\InvalidArgumentException(
                sprintf("The routes part is missing in the configuration")
            );
        }

        array_walk($config['routes'], 'self::checkRoutes');
    }

    /**
     * Check routes from configuration
     *
     * @param  array  $data
     * @param  string $name
     * @throws Exception\InvalidArgumentException
     */
    protected static function checkRoutes(array $data, $name)
    {
        if (!isset($data['url'])) {
            throw new Exception\InvalidArgumentException(
                sprintf("The url parameter is missing in route %s", $name)
            );
        }

        if (isset($data['children'])) {
            return array_walk($data['children'], 'self::checkRoutes');
        }

        if (!isset($data['action'])) {
            throw new Exception\InvalidArgumentException(
                sprintf("The action parameter is missing in route %s", $name)
            );
        }
    }
}
