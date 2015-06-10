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

    public static function factory(array $config)
    {
        self::checkConfig($config);
        $router = isset($config['router']['adapter']) ?
                  $config['router']['adapter'] :
                  self::DEFAULT_ROUTER;
        return new Dispatcher(new $router($config));
    }

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
        foreach ($config['routes'] as $name => $data) {
            if (!isset($data['action'])) {
                throw new Exception\InvalidArgumentException(
                    sprintf("The action parameter is missing in route %s", $name)
                );
            }
            if (!isset($data['url'])) {
                throw new Exception\InvalidArgumentException(
                    sprintf("The url parameter is missing in route %s", $name)
                );
            }
        }
    }
}
