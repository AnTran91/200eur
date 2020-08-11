<?php
/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use App\Handlers\OrderHandler;

/**
 * Handler for clearing invalidating the current session.
 */
class SessionLogoutHandler implements LogoutHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var OrderHandler
     */
    private $orderHandler;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface  $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher, OrderHandler $orderHandler)
    {
        $this->dispatcher = $dispatcher;
        $this->orderHandler = $orderHandler;
    }

    /**
     * Invalidate the current session.
     *
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {

    }
}
