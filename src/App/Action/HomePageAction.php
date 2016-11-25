<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

class HomePageAction
{
    private $router;
    private $template;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template = null
    ) {
        $this->router = $router;
        $this->template = $template;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        return new HtmlResponse($this->template->render('app::home-page', []));
    }
}