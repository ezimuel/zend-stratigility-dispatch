<?php
namespace ZendTest\Stratigility\Dispatch\TestAsset;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Home {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return true;
    }
}
