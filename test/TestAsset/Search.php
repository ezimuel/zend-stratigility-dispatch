<?php
namespace ZendTest\Stratigility\Dispatch\TestAsset;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Search {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (!empty($request->getAttribute('query'))) {
          return $request->getAttribute('query');
        }
        return true;
    }
}
