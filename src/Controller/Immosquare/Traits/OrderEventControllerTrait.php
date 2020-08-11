<?php

namespace App\Controller\Immosquare\Traits;

use App\Entity\Order;
use App\Events\OrderEvent;
use App\Exception\ApiProblem;
use App\Exception\ApiProblemException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extra features needed in controllers.
 */
trait OrderEventControllerTrait
{
    /**
     * Fire save order event with status (waiting for payment)
     *
     * @param array $options
     * @return Order
     */
    public function fireOrderEvent(array $options): Order
    {
        $resolver = new OptionsResolver();
        $this->configureOrderEventOptions($resolver);

        $options = $resolver->resolve($options);

        $event = new OrderEvent($options['data'], $this->getUser(), $options['order'], $options['transaction'], $options['order_status']);
        $this->dispatch($options['event_name'], $event);

        if ($event->isPropagationStopped() || count($event->getErrorMsg()) > 0) {
            $this->throwApiProblemOrderException($this->trans($options['order_error_msg']), $event->getErrorType(), $event->getErrorMsg());
        }

        return $event->getOrder();
    }

    /**
     * @param null|string   $msg
     * @param null|string   $errorType
     * @param array         $cause
     */
    public function throwApiProblemOrderException(?string $msg, ?string $errorType, ?array $cause)
    {
        $apiProblem = null;

        if ($errorType == OrderEvent::TYPE_VALIDATION_ERROR){
            $apiProblem = new ApiProblem(
                400,
                ApiProblem::TYPE_VALIDATION_ERROR
            );

            $apiProblem->set('errors', $cause);
        }

        if ($errorType == OrderEvent::TYPE_ORDER_ERROR){
            $apiProblem = new ApiProblem(
                200,
                ApiProblem::TYPE_ORDER_ERROR
            );

            $apiProblem->set('message', $msg);
            if (!empty($cause)){
                $apiProblem->set('cause', $cause);
            }
        }

        if (!is_null($apiProblem)){
            throw new ApiProblemException($apiProblem);
        }

        throw new \LogicException('none reachble bloc');
    }

    /**
     * configure OptionsResolver
     *
     * @param OptionsResolver $resolver
     */
    public function configureOrderEventOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data' => null,
            'order' => null,
            'transaction' => null,
            'order_status' => Order::AWAITING_FOR_PAYMENT,
            'event_name' => null,
            'order_error_msg' => 'orders.msg.order_creation_error',
            'response_type' => null,
            'order_identifier' => 'id',
            'route_name' => null,
            'forward_action' => null,
        ));

        $resolver->setAllowedTypes('data', ['null', 'array']);
        $resolver->setAllowedTypes('order', ['null', '\App\Entity\Order']);
        $resolver->setAllowedTypes('transaction', ['null', '\App\Entity\Transaction']);
        $resolver->setAllowedTypes('order_status', ['null', 'string']);
        $resolver->setAllowedTypes('event_name', ['null', 'string']);
        $resolver->setAllowedTypes('order_error_msg', ['string']);
        $resolver->setAllowedTypes('response_type', ['null', 'string']);
        $resolver->setAllowedTypes('route_name', ['null', 'string']);
        $resolver->setAllowedTypes('order_identifier', ['null', 'string']);
        $resolver->setAllowedTypes('forward_action', ['null', 'string']);
        $resolver->setAllowedValues('response_type', ['json', 'forward', null]);

        $resolver->setRequired(['event_name']);
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
}
