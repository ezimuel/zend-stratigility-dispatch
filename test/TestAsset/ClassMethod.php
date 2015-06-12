<?php
namespace ZendTest\Stratigility\Dispatch\TestAsset;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ClassMethod
{
    public static function myMethod(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return true;
    }
}
