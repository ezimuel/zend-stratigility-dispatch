<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Stratigility\Dispatch\Router;

use Aura\Router\Generator;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Router;

class Aura implements RouterInterface
{
    /**
     * Aura router
     *
     * @var Aura\Router\Router
     */
    protected $router;

    /**
     * Matched Aura route
     *
     * @var Aura\Router\Route
     */
    protected $route;

    /**
     * Router configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->createRouter();
    }

    /**
     * Create the Aura router instance
     */
    protected function createRouter()
    {
        $this->router = new Router(
            new RouteCollection(new RouteFactory()),
            new Generator()
        );
    }

    /**
     * Set config
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        if (!empty($this->config)) {
            $this->createRouter();
        }

        foreach ($config['routes'] as $name => $data) {
            if (isset($data['children'])) {
                $this->router->attach($name, $data['url'], function ($router) use ($data) {
                    if (isset($data['tokens'])) {
                        $router->addTokens($data['tokens']);
                    }
                    if (isset($data['values'])) {
                        $router->addValues($data['values']);
                    }

                    foreach ($data['children'] as $name => $data) {
                        $data['values'] = isset($data['values']) ? $data['values'] : [];
                        $data['values']['action'] = $data['action'];

                        $route = $router->add($name, $data['url']);
                        $route->addValues($data['values']);

                        if (isset($data['tokens'])) {
                            $route->addTokens($data['tokens']);
                        }
                    }
                });

                continue ;
            }

            $this->router->add($name, $data['url']);
            if (!isset($data['values'])) {
                $data['values'] = [];
            }
            $data['values']['action'] = $data['action'];
            if (!isset($data['tokens'])) {
                $this->router->add($name, $data['url'])
                             ->addValues($data['values']);
            } else {
                $this->router->add($name, $data['url'])
                             ->addTokens($data['tokens'])
                             ->addValues($data['values']);
            }
        }
        $this->config = $config;
    }

    /**
     * Get config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param  string $patch
     * @param  array $params
     * @return boolean
     */
    public function match($path, $params)
    {
        $this->route = $this->router->match($path, $params);
        return (false !== $this->route);
    }

    /**
     * @return array
     */
    public function getMatchedParams()
    {
        return $this->route->params;
    }

    /**
     * @return string
     */
    public function getMatchedRouteName()
    {
        return $this->route->name;
    }

    /**
     * @return mixed
     */
    public function getMatchedAction()
    {
        return $this->route->params['action'];
    }
}
