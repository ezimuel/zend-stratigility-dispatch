<?php
namespace ZendTest\Stratigility\Dispatch\TestAsset;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Page {

    protected $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }

    public function action(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return true;
    }
}
