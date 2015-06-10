<?php
return [
    'router' => [
        'adapter' => 'Zend\Stratigility\Dispatch\Router\Aura'
    ],
    'routes' => [
        // example of action class without dependencies
        'home' => [
            'url'    => '/',
            'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Home'
        ],
        // example of action class with dependencies using a factory
        'page' => [
            'url' => '/page',
            'action' => function($request, $response, $next) {
                $bar  = new ZendTest\Stratigility\Dispatch\TestAsset\Bar();
                $page = new ZendTest\Stratigility\Dispatch\TestAsset\Page($bar);
                return $page->action($request, $response, $next);
            }
        ],
        // example of action class using optional parameter in the URL
        // the syntax of the URL and the tokens depend on the router adapter (Aura in this case)
        'search' => [
            'url'    => '/search{/query}',
            'tokens' => [ 'query' => '([^/]+)?' ],
            'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Search'
        ]
    ]
];
