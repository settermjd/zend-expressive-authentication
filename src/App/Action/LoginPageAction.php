<?php
namespace App\Action;

use App\Entity\AuthUserInterface;
use App\Repository\UserAuthenticationInterface;
use PSR7Session\Http\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode\RFC\RFC7231;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
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
     * @var string
     */
    private $defaultRedirectUri;

    /**
     * LoginPageAction constructor.
     * @param Router\RouterInterface $router
     * @param Template\TemplateRendererInterface|null $template
     * @param UserAuthenticationInterface $userAuthenticationService
     * @param AuthUserInterface $authenticationEntity
     * @param string $defaultRedirectUri
     */
    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template,
        UserAuthenticationInterface $userAuthenticationService,
        AuthUserInterface $authenticationEntity,
        $defaultRedirectUri
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->authEntity = $authenticationEntity;
        $this->form = (new AnnotationBuilder())->createForm($this->authEntity);
        $this->userAuthenticationService = $userAuthenticationService;
        $this->defaultRedirectUri = $defaultRedirectUri;
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
                return new RedirectResponse(
                    $this->getRedirectUri($request),
                    RFC7231::FOUND
                );
            } catch (UserAuthenticationException $e) {
                return $this->renderLoginFormResponse();
            }
        }

        return $this->renderLoginFormResponse();
    }

    /**
     * Setup and perform form-based validation
     *
     * It's a utility method to make the __invoke method easier to read
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function doValidation(ServerRequestInterface $request)
    {
        return $this->form
            ->bind($this->authEntity)
            ->setData($request->getParsedBody())
            ->isValid();
    }

    /**
     * Render an HTML reponse, containing the login form
     *
     * Provide the functionality required to let a user authenticate, based on using an HTML form.
     *
     * @return HtmlResponse
     */
    private function renderLoginFormResponse()
    {
        return new HtmlResponse($this->template->render(self::PAGE_TEMPLATE, [
            'form' => $this->form,
        ]));
    }

    /**
     * Get the URL to redirect the user to
     *
     * The value returned here is where to send the user to after a successful authentication has
     * taken place. The intent is to avoid the user being redirected to a generic route after
     * login, requiring them to have to specify where they want to navigate to.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getRedirectUri(ServerRequestInterface $request)
    {
        if (array_key_exists('redirect_to', $request->getQueryParams())) {
            return $request->getQueryParams()['redirect_to'];
        }

        return $this->defaultRedirectUri;
    }
}