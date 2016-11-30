<?php

namespace App;

use App\Action\LoginPageAction;
use App\Action\LoginPageFactory;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationMiddlewareFactory;
use App\Repository\UserAuthenticationInterface;
use App\Service\UserAuthenticationServiceClientFactory;
use App\Service\UserRestfulAuthenticationServiceFactory;
use GuzzleHttp\ClientInterface;

/**
 * Class ConfigProvider
 * @package App
 */
class ConfigProvider
{
    /**
     * Provide the configuration for the module
     *
     * This class handles the setup of the configuration for this module. This
     * configuration will be merged into the wider application configuration if
     * it's enabled.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'routes' => $this->getRouteConfig(),
            'app' => $this->getAppConfig()
        ];
    }

    /**
     * @return array
     */
    public function getAppConfig()
    {
        return [
            'authentication' => [
                'default_redirect_to' => '/',
                'service' => [
                    'host' => 'http://127.0.0.1',
                    'port' => 18080,
                    'base_path' => '/',
                ]
            ],
        ];
    }

    /**
     * Provides the namespace's route configuration
     *
     * @return array
     */
    public function getRouteConfig()
    {
        return [
            [
                'name' => 'login',
                'path' => '/login',
                'middleware' => LoginPageAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
        ];
    }

    /**
     * Provides the namespace's dependency configuration
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'factories' => [
                AuthenticationMiddleware::class => AuthenticationMiddlewareFactory::class,
                LoginPageAction::class => LoginPageFactory::class,

                /**
                 * Register a class that will handle the user authentication.
                 * */
                UserAuthenticationInterface::class => UserRestfulAuthenticationServiceFactory::class,

                /**
                 * Register a remote service client
                 */
                ClientInterface::class => UserAuthenticationServiceClientFactory::class
            ],
            'aliases' => []
        ];
    }
}
