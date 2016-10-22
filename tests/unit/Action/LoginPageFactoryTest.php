<?php

use App\Action\LoginPageFactory;
use App\Repository\UserAuthenticationInterface;
use Codeception\Test\Unit;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LoginPageFactoryTest extends Unit
{
    private $userAuthService;
    private $template;
    private $router;
    private $containerInterface;

    public function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);
        $this->template = $this->prophesize(TemplateRendererInterface::class);
        /** @var UserAuthenticationInterface|\Prophecy\Prophecy\ObjectProphecy userAuthService */
        $this->userAuthService = $this->prophesize(UserAuthenticationInterface::class);
        /** @var \Interop\Container\ContainerInterface|\Prophecy\Prophecy\ObjectProphecy containerInterface */
        $this->containerInterface = $this->prophesize(\Interop\Container\ContainerInterface::class);
    }

    /**
     * @covers LoginPageFactory
     */
    public function testCanBeInstantiated()
    {
        $factory = new LoginPageFactory();
        $this->assertTrue(
            $factory instanceof \Interop\Config\RequiresConfig,
            'does not implement RequiresConfig'
        );
    }

    public function testLoginPageActionInvokation()
    {
        $factory = new LoginPageFactory();
        $this->containerInterface
            ->get('config')
            ->willReturn([
                'app' => [
                    'authentication' => [
                        'default_redirect_to' => '/',
                    ]
                ]
            ]);
        $this->containerInterface
            ->get(RouterInterface::class)
            ->willReturn($this->router->reveal());
        $this->containerInterface
            ->get(TemplateRendererInterface::class)
            ->willReturn($this->template);
        $this->containerInterface
            ->get(UserAuthenticationInterface::class)
            ->willReturn($this->userAuthService);
        $this->containerInterface
            ->has(TemplateRendererInterface::class)
            ->willReturn(true);
        $action = $factory($this->containerInterface->reveal());
    }
}
