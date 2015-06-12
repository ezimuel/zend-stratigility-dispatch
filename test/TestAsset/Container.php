<?php
namespace ZendTest\Stratigility\Dispatch\TestAsset;

use Interop\Container\ContainerInterface;

class Container implements ContainerInterface
{
    protected $container = [];

    public function get($id)
    {
        if ($this->has($id)) {
            return $this->container[$id];
        }
        return false;
    }

    public function set($id, $value)
    {
        $this->container[$id] = $value;
    }

    public function has($id)
    {
        return isset($this->container[$id]);
    }
}
