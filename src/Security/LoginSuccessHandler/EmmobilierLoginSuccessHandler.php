<?php

namespace App\Security\LoginSuccessHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Custom authentication success handler.
 */
class EmmobilierLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $security;

    /**
     * Constructor
     *
     * @param RouterInterface               $router
     * @param AuthorizationCheckerInterface $security
     */
    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $security)
    {
        $this->router   = $router;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $request = $event->getRequest();
        $this->onAuthenticationSuccess($request, $token);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!empty($user->getLanguage())) {
            $request->getSession()->set('_locale', $user->getLanguage());
        }

        return new RedirectResponse($this->router->generate('order_list'));
    }
}