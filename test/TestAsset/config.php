<?php
return [
    'route' => [
          // example of action class without dependencies
          'home' => [
              'url'    => '/',
              'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Home',
              'tokens' => [],
              'values' => [],
          ],
          // example of action class with dependencies
          'page' => [
              'url' => '/page',
              'action' => function($request, $response, $next) {
                  $bar  = new ZendTest\Stratigility\Dispatch\TestAsset\Bar();
                  $page = new ZendTest\Stratigility\Dispatch\TestAsset\Page($bar);
                  return $page->action($request, $response, $next);
              }
          ],
          'search' => [
              'url'    => '/search{/query}',
              'tokens' => [ 'query' => '([^/]+)?' ],
              'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Search'
          ]
    ]
];
