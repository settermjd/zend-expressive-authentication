<?php
namespace App\Action;

use App\Entity\LoginUser;
use App\Repository\UserAuthenticationInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LoginPageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $userRepository = $container->get(UserAuthenticationInterface::class);
        $userEntity = new LoginUser();

        return new LoginPageAction($router, $template, $userRepository, $userEntity);
    }
}