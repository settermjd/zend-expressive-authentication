<?php
namespace App\Middleware;

use Psr\Http\Message\UriInterface;
use PSR7Session\Http\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode\RFC\RFC7231;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

/**
 * Class AuthenticationMiddleware
 * @package App\Middleware
 */
class AuthenticationMiddleware
{
    /**
     * @var Router\RouterInterface
     */
    private $router;

    /**
     * @var null|Template\TemplateRendererInterface
     */
    private $template;

    /**
     * AuthenticationMiddleware constructor
     *
     * @param Router\RouterInterface $router
     * @param Template\TemplateRendererInterface $template
     */
    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template
    ) {
        $this->router = $router;
        $this->template = $template;
    }

    /**
     * Handle the authentication of a user
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return RedirectResponse|HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if ($session->get('id') === null) {
            return new RedirectResponse(
                sprintf('/login?redirect_to=%s', $this->getCurrentRequest($request)),
                RFC7231::FOUND
            );
        }

        return $next($request, $response);
    }

    /**
     * Retrieve the current request
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getCurrentRequest(ServerRequestInterface $request)
    {
        /** @var UriInterface $uri */
        $uri = $request->getUri();

        $redirectTo = $uri->getPath();

        if ($uri->getQuery() !== '') {
            $redirectTo .= '?' . $uri->getQuery();
        }

        if ($uri->getFragment() !== '') {
            $redirectTo .= '#' . $uri->getFragment();
        }

        return urlencode($redirectTo);
    }
}