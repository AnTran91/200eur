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
class AdminLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
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
     * @var array
     */
    private $userInChargeOnTheOrderSection;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param AuthorizationCheckerInterface $security
     */
    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $security)
    {
        $this->router   = $router;
        $this->security = $security;
    }

    /**
     * @param array $userInChargeOnTheOrderSection
     */
    public function setUserInChargeOnTheOrderSection(array $userInChargeOnTheOrderSection): void
    {
        $this->userInChargeOnTheOrderSection = $userInChargeOnTheOrderSection;
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
        $request->getSession()->set('_locale', 'fr');

        if ($this->isGrantedInOrderSection()) {
            return new RedirectResponse($this->router->generate('admin_order_index'));
        }

        return new RedirectResponse($this->router->generate('dashboard'));
    }

    private function isGrantedInOrderSection()
    {
        foreach ($this->userInChargeOnTheOrderSection as $role){
            if ($this->security->isGranted($role)){
                return true;
            }
        }

        return false;
    }
}