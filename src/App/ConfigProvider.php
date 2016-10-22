<?php

namespace App;

use App\Action\LoginPageAction;
use App\Action\LoginPageFactory;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthenticationMiddlewareFactory;
use App\Repository\UserAuthenticationFactory;
use App\Repository\UserAuthenticationInterface;
use App\Repository\UserTableAuthentication;

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
            ]
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
                 * The one registered here provides only a generic sample implementation
                 * and is not meant to be taken seriously.
                 *
                 * UserTableAuthentication::class => UserAuthenticationFactory::class,
                 * */
            ],
            'aliases' => [
                /**
                 * This is a sample setup whereby the specific implementation is never
                 * referenced anywhere in the codebase, instead using a generic alias.
                 *
                 * UserAuthenticationInterface::class => UserTableAuthentication::class
                 * */
            ]
        ];
    }
}
