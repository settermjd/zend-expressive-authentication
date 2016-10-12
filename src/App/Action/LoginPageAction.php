<?php
namespace App\Action;

use App\Entity\AuthUserInterface;
use App\Repository\UserAuthenticationInterface;
use PSR7Session\Http\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;

/**
 * Class LoginPageAction
 * @package App\Action
 */
class LoginPageAction
{
    /**
     * @var string
     */
    const PAGE_TEMPLATE = 'app::login-page';

    /**
     * @var Router\RouterInterface
     */
    private $router;

    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UserAuthenticationInterface
     */
    private $userAuthenticationService;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var AuthUserInterface
     */
    private $authEntity;

    /**
     * LoginPageAction constructor.
     * @param Router\RouterInterface $router
     * @param Template\TemplateRendererInterface|null $template
     * @param UserAuthenticationInterface $userAuthenticationService
     * @param AuthUserInterface $authenticationEntity
     */
    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template,
        UserAuthenticationInterface $userAuthenticationService,
        AuthUserInterface $authenticationEntity
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->authEntity = $authenticationEntity;
        $this->form = (new AnnotationBuilder())->createForm($this->authEntity);
        $this->userAuthenticationService = $userAuthenticationService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if ($request->getMethod() === 'POST' && $this->doValidation($request, $session)) {
            $user = $this->form->getData();
            try {
                $session->set('id', $this->userAuthenticationService->authenticateUser(
                    $user->getUsername(),
                    $user->getPassword()
                ));
                return $next($request, $response);
            } catch (UserAuthenticationException $e) {
                return $this->renderLoginFormResponse();
            }
        }

        return $this->renderLoginFormResponse();
    }

    /**
     * Setup and perform form validation
     *
     * It's a simple utility method to make the __invoke method easier to read
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function doValidation(ServerRequestInterface $request)
    {
        $this->form
            ->bind($this->authEntity)
            ->setData($request->getParsedBody());

        return $this->form->isValid();
    }

    /**
     * @return HtmlResponse
     */
    private function renderLoginFormResponse()
    {
        return new HtmlResponse($this->template->render(self::PAGE_TEMPLATE, [
            'form' => $this->form,
        ]));
    }
}