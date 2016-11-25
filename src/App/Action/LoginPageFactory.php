<?php
namespace App\Action;

use App\Entity\LoginUser;
use App\Repository\UserAuthenticationInterface;
use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfigId;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LoginPageFactory implements RequiresConfigId, ProvidesDefaultOptions, RequiresMandatoryOptions
{
    use ConfigurationTrait;

    public function dimensions()
    {
        return ['app'];
    }

    public function mandatoryOptions()
    {
        return [
            'default_redirect_to'
        ];
    }

    public function defaultOptions()
    {
        return [
            'status_code' => 302,
        ];
    }

    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $userRepository = $container->get(UserAuthenticationInterface::class);
        $userEntity = new LoginUser();

        if ($this->canRetrieveOptions($container->get('config'), 'authentication')) {
            $authenticationOptions = $this->options($container->get('config'), 'authentication');
        }

        return new LoginPageAction(
            $router,
            $template,
            $userRepository,
            $userEntity,
            $authenticationOptions['default_redirect_to']
        );
    }
}