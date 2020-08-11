<?php

namespace App\Controller\Emmobilier\Traits;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

/**
 * Extra features needed in controllers.
 */
trait ControllerTrait
{
    /**
     * Get Error Messages From the Form with keys.
     *
     * @param FormInterface  $form    The current form that contains errors
     *
     * @return array The array of errors
     */
    public function getErrorMessages(FormInterface $form): array
    {
        return \App\Utils\Tools::getErrorMessages($form);
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     *
     * @return string The translated string
     */
    public function trans(string $id, array $parameters = array(), ?string $domain = null): ?string
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null): ?string
    {
        return $this->container->get('translator')->transChoice($id, $number, $parameters, $domain);
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     * @param Event  $event     The event to pass to the event handlers/listeners
     *                          If not supplied, an empty Event instance is created
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event = null): ?Event
    {
        return $this->container->get('event_dispatcher')->dispatch($eventName, $event);
    }
    
	/**
	 * Retrieves a FilterCollection instance from the given ObjectManager.
	 *
	 * @throws \Gedmo\Exception\InvalidArgumentException
	 * @return mixed
	 */
	public function getFilters()
	{
		$om = $this->container->get('doctrine.orm.default_entity_manager');
		
		if (is_callable(array($om, 'getFilters'))) {
			return $om->getFilters();
		} else {
			if (is_callable(array($om, 'getFilterCollection'))) {
				return $om->getFilterCollection();
			}
		}
		throw new \Gedmo\Exception\InvalidArgumentException("ObjectManager does not support filters");
	}
	
	/**
	 * Return an instance of orderHandler form the container
	 *
	 * @return \App\Handlers\OrderHandler
	 */
	public function getOrderHandler()
	{
		return $this->container->get('App\Handlers\OrderHandler');
	}
}
