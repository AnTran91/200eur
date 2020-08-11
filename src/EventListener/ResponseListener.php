<?php

namespace App\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseListener implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			KernelEvents::RESPONSE => 'onKernelResponse',
		);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function onKernelResponse(FilterResponseEvent $event)
	{
		$event->getResponse()->headers->set('Cache-Control', 'no-store, must-revalidate');
		$event->getResponse()->headers->set('Pragma', 'no-cache');
		$event->getResponse()->headers->set('Expires', '0');
	}
}