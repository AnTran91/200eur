<?php

namespace Sentry\SentryBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

interface SentryExceptionListenerInterface
{
    /**
     * Used to capture information from the request before any possible error
     * event is encountered by listening on core.request.
     *
     * Most commonly used for assigning the username to the security context
     * used by Sentry for each request.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void;

    /**
     * When an exception occurs as part of a web request, this method will be
     * triggered for capturing the error.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void;

    /**
     * When an exception occurs on the command line, this method will be
     * triggered for capturing the error.
     *
     * @param ConsoleErrorEvent $event
     */
    public function onConsoleError(ConsoleErrorEvent $event): void;

    /**
     * When an exception occurs on the command line, this method will be
     * triggered for capturing the error.
     *
     * @param ConsoleExceptionEvent $event
     * @deprecated This method exists for BC with Symfony 3.x
     */
    public function onConsoleException(ConsoleExceptionEvent $event): void;
}
