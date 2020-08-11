<?php

namespace App\EventSubscriber\OrderCreation;

use App\Entity\Order;
use App\Exception\FileSystemException;
use App\Handlers\ParamHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Events\OrderEvent;

use App\Utils\Events;

use App\Handlers\OrderBuilder;

use App\Handlers\FileHandler;

use Doctrine\ORM\EntityManagerInterface;

use App\Exception\OrderException;

/**
 * @see \App\Events\OrderEvent class.
 */
final class ImmosquareOrderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var OrderBuilder
     */
    private $builder;

    /**
     * @var FileHandler
     */
    private $uploaderHandler;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ParamHandler
     */
    private $paramHandler;

    /**
     * Constructor
     *
     * @param OrderBuilder $builder
     * @param ParamHandler $paramHandler
     * @param FileHandler $uploader
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(OrderBuilder $builder, ParamHandler $paramHandler, FileHandler $uploader, EntityManagerInterface $em)
    {
        $this->builder = $builder;
        $this->uploaderHandler = $uploader;
        $this->em = $em;
        $this->paramHandler = $paramHandler;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            Events::ON_CREATE_IMMOSQUARE_ORDER => array(
                array('onValidateParams', 20),
                array('onPreCreateOrder', 10),
                array('onCreateOrder', 0),
                array('onPostCreateOrder', -10)
            )
        ];
    }

    /**
     * Params validation
     *
     * @param OrderEvent $orderEvent
     */
    public function onValidateParams(OrderEvent $orderEvent): void
    {
        $settings = $this->paramHandler->handleImmosquareParams($orderEvent->getData());

        if (count($settings['errors']) > 0){
            $orderEvent->setErrorType(OrderEvent::TYPE_VALIDATION_ERROR);
            $orderEvent->setErrorMsg($settings['errors']);
            $orderEvent->stopPropagation();
        }

        $orderEvent->setData(array_replace_recursive($orderEvent->getData(), $settings['data']));
    }

    /**
     * Pre Create the OrderCreation
     *
     * @param OrderEvent $orderEvent
     */
    public function onPreCreateOrder(OrderEvent $orderEvent)
    {
        try {
            $orderEvent->setUploadFolder($this->uploaderHandler->getOrderUniqueDir($orderEvent->getUser()->getUserDirectory()));
        } catch (FileSystemException $e) {
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }
	
	/**
	 * Save the order
	 *
	 * @param OrderEvent $orderEvent
	 *
	 * @throws \Doctrine\DBAL\ConnectionException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function onCreateOrder(OrderEvent $orderEvent)
    {
        try {
            $data = $orderEvent->getData();
            $this->builder->reset()
                ->setUser($orderEvent->getUser())
                ->setUploadFolder($orderEvent->getUploadFolder())
                ->setOrderInProduction()
                ->setOrderDeliveryTime($data['delivery_time'])
                ->setPictures($data['images'])
                ->setOrderType(Order::IMMOSQUARE_TYPE)
                ->setOrderAmount()
                ->generateOrderNumber()
            ;

            $orderEvent->setOrder($this->builder->getOrder());
        } catch (OrderException $e) {
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * After Create the OrderCreation
     *
     * @param OrderEvent $orderEvent
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onPostCreateOrder(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();

            // suspend auto-commit
            $this->em->getConnection()->beginTransaction();

            $this->em->persist($order);
            $this->em->flush();

            // Try and commit the transaction
            $this->em->getConnection()->commit();

            $orderEvent->setOrder($order);
        } catch (\Exception $e) {
            // Rollback the failed transaction attempt
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }
}
