<?php

use App\Middleware\AuthenticationMiddleware;
use App\Entity\LoginUser;
use App\Repository\UserAuthenticationInterface;
use Codeception\Test\Unit;
use Prophecy\Argument\Token\AnyValueToken;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Session\SessionInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LoginPageActionTest extends Unit
{
    private $userAuthService;
    private $template;
    private $router;

    public function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);
        $this->template = $this->prophesize(TemplateRendererInterface::class);

        $out = uniqid();
        $this->template
            ->render(new AnyValueToken(), new AnyValueToken())
            ->willReturn($out);

        /** @var UserAuthenticationInterface|\Prophecy\Prophecy\ObjectProphecy userAuthService */
        $this->userAuthService = $this->prophesize(UserAuthenticationInterface::class);
    }

    /**
     * @covers AuthenticationMiddleware
     */
    public function testRendersLoginFormOnGetRequest()
    {
        $action = new AuthenticationMiddleware(
            $this->router->reveal(),
            $this->template->reveal(),
            $this->userAuthService->reveal(),
            new LoginUser()
        );

        $session = $this->prophesize(SessionInterface::class);
        $session
            ->get('id')
            ->willReturn(null);
        $request = $this->prophesize(ServerRequest::class);
        $request
            ->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)
            ->willReturn($session->reveal());
        $request
            ->getUri()
            ->willReturn(new Uri('http://localhost:8080/'));
        $request
            ->getMethod()
            ->willReturn('GET');

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $action(
            $request->reveal(), new Response(), function () {
            return new ServerRequest([], [], '/', 'GET');
        });

        $this->assertTrue($response instanceof Response, 'incorrect Response object returned');
        $this->assertSame(302, $response->getStatusCode(), 'incorrect status code set');
    }

    /**
     * @covers AuthenticationMiddleware
     */
    public function testRendersNextRequestWhenAuthenticated()
    {
        $action = new AuthenticationMiddleware(
            $this->router->reveal(),
            $this->template->reveal(),
            $this->userAuthService->reveal(),
            new LoginUser()
        );

        $session = $this->prophesize(SessionInterface::class);
        $session
            ->get('id')
            ->willReturn(1);
        $request = $this->prophesize(ServerRequest::class);
        $request
            ->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)
            ->willReturn($session->reveal());
        $request
            ->getUri()
            ->willReturn(new Uri('http://localhost:8080/'));

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $action(
            $request->reveal(), new Response(), function () {
            return new ServerRequest([], [], '/', 'GET');
        });

        $this->assertTrue($response instanceof ServerRequestInterface, 'incorrect object returned');
    }

    /**
     * @covers AuthenticationMiddleware
     */
    public function testPassesThroughToNextMiddlewareOnSuccessfulAuthentication()
    {
        $action = new AuthenticationMiddleware(
            $this->router->reveal(),
            $this->template->reveal(),
            $this->userAuthService->reveal(),
            new LoginUser()
        );

        $data = [
            'username' => 'username',
            'password' => 'password'
        ];

        $request = $this->prophesize(ServerRequest::class);
        $session = $this->prophesize(SessionInterface::class);
        $session
            ->get('id')
            ->willReturn(null);
        $session
            ->set('id', 1)
            ->willReturn(null);
        $request
            ->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)
            ->willReturn($session->reveal());
        $request
            ->getMethod()
            ->willReturn('POST');
        $request
            ->getUri()
            ->willReturn(new Uri('http://localhost:8080/'));
        $request
            ->getParsedBody()
            ->willReturn($data);
        $this->userAuthService
            ->authenticateUser($data['username'], $data['password'])
            ->willReturn(1);

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $action(
            $request->reveal(), new Response(), function () {
            return new ServerRequest([], [], '/', 'GET');
        });

        $this->assertTrue($response instanceof Response\RedirectResponse, 'Should have returned a RedirectResponse object');
    }

    /**
     * @covers AuthenticationMiddleware
     */
    public function testRendersLoginFormOnUnsuccessfulAuthentication()
    {
        $action = new AuthenticationMiddleware(
            $this->router->reveal(),
            $this->template->reveal(),
            $this->userAuthService->reveal(),
            new LoginUser()
        );

        $data = [
            'username' => 'username',
            'password' => 'password'
        ];

        $request = $this->prophesize(ServerRequest::class);
        $session = $this->prophesize(SessionInterface::class);
        $session
            ->get('id')
            ->willReturn(null);
        $session
            ->set('id', 1)
            ->willReturn(null);
        $request
            ->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)
            ->willReturn($session->reveal());
        $request
            ->getMethod()
            ->willReturn('POST');
        $request
            ->getUri()
            ->willReturn(new Uri('http://localhost:8080/'));
        $request
            ->getParsedBody()
            ->willReturn($data);
        $this->userAuthService
            ->authenticateUser($data['username'], $data['password'])
            ->willThrow(new \App\Repository\UserAuthenticationException());

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $action(
            $request->reveal(), new Response(), function () {}
        );

        $this->assertTrue($response instanceof Response, 'incorrect Response object');
        $this->assertSame(302, $response->getStatusCode(), 'incorrect status code set');
    }
}
