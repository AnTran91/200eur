<?php

namespace App\EventListener;


use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use FOS\UserBundle\Controller\SecurityController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginSubscriber implements EventSubscriberInterface
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
    private $emmobilierRoles;
    private $adminRoles;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param AuthorizationCheckerInterface $security
     * @param array $emmobilierRoles
     * @param array $adminRoles
     */
    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $security, array $emmobilierRoles, array $adminRoles)
    {
        $this->router   = $router;
        $this->security = $security;
        $this->emmobilierRoles = $emmobilierRoles;
        $this->adminRoles = $adminRoles;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => [
                array('onEmmobilierLoginController', 10),
                array('onAdminLoginController', 20)
                //array('onResidenceLoginController', 30),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onAdminLoginController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller) || $controller[1] != 'loginAction' || !$controller[0] instanceof \App\Controller\Admin\SecurityController) {
            return;
        }

        if ($event->getRequest()->isXmlHttpRequest()) {
            throw new BadRequestHttpException("Error Processing Request");
        }

        if ($this->isGranted($this->adminRoles)) {
            $redirection =  new RedirectResponse($this->router->generate('dashboard'));
            $redirection->send();
        }
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function onResidenceLoginController(FilterControllerEvent $event)
//    {
//        $controller = $event->getController();
//        /*
//         * $controller passed can be either a class or a Closure.
//         * This is not usual in Symfony but it may happen.
//         * If it is a class, it comes in array format
//         */
//        if (!is_array($controller) || $controller[1] != 'loginAction' || !$controller[0] instanceof SecurityController) {
//            return;
//        }
//
//        if ($event->getRequest()->isXmlHttpRequest()) {
//            throw new BadRequestHttpException("Error Processing Request");
//        }
//
//        if ($this->isGranted($this->adminRoles)) {
//            $redirection =  new RedirectResponse($this->router->generate('dashboard'));
//            $redirection->send();
//        }
//    }

    /**
     * {@inheritdoc}
     */
    public function onEmmobilierLoginController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller) || $controller[1] != 'loginAction' || !$controller[0] instanceof SecurityController) {
            return;
        }

        if ($event->getRequest()->isXmlHttpRequest()) {
            throw new BadRequestHttpException("Error Processing Request");
        }

        if ($this->isGranted($this->emmobilierRoles)) {
            $redirection =  new RedirectResponse($this->router->generate('order_list'));
            $redirection->send();
        }
    }

    private function isGranted($roles)
    {
        foreach ($roles as $role){
            if ($this->security->isGranted($role)){
                return true;
            }
        }
        return false;
    }
}