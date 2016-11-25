<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
        // Map middleware -> factories here
        'factories' => [
        ],
    ],

    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' =>
                [
                    \App\Middleware\AuthenticationMiddleware::class,
                    App\Action\HomePageAction::class,
                ],
            'allowed_methods' => ['GET'],
        ],
    ],
];
