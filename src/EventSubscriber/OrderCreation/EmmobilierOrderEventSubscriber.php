<?php

namespace App\EventSubscriber\OrderCreation;

use App\Exception\FileSystemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Events\OrderEvent;

use App\Utils\Events;

use App\Handlers\OrderBuilder;

use App\Handlers\FileHandler;

use Doctrine\ORM\EntityManagerInterface;

use App\Exception\OrderException;
use Doctrine\DBAL\ConnectionException;

use App\Entity\Order;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @see \App\Events\OrderEvent class.
 */
final class EmmobilierOrderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var OrderBuilder
     */
    private $builder;

    /**
     * @var SessionInterface
     */
    private $sessionManager;

    /**
     * @var FileHandler
     */
    private $uploaderHandler;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor
     *
     * @param SessionInterface $session
     * @param OrderBuilder $builder
     * @param FileHandler $uploader
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(SessionInterface $session, OrderBuilder $builder, FileHandler $uploader, EntityManagerInterface $em)
    {
        $this->sessionManager = $session;
        $this->builder = $builder;
        $this->uploaderHandler = $uploader;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          Events::ON_SAVE_ORDER => array(
            array('onPreCreateOrder', 10),
            array('onSaveOrder', 0),
            array('onPostCreateOrder', -10),
            array('onClearOrderData', -30)
          ),
          Events::ON_FREE_ORDER => array(
            array('onUpdateFreeOrder', 0)
          ),
          Events::ON_PAY_ORDER_BY_WALLET => array(
            array('onUpdateOrderAndUserWallet', 0)
          ),
          Events::ON_PAY_ORDER_BY_TRANSACTION => array(
            array('onUpdateOrderAndValidateTransaction', 0)
          ),
          Events::ON_SAVE_MONTHLY_ORDER => array(
            array('onUpdateUserMonthlyOrder', 0)
          ),
          Events::ON_SAVE_ORDER_WHEN_DISCONNECT => array(
            array('onPreCreateOrder', 10),
            array('onSaveOrder', 0),
            array('onPostCreateOrder', -10),
            array('onClearOrderData', -30)
          )
        ];
    }

    /**
     * Pre Create the OrderCreation
     *
     * @param OrderEvent $orderEvent
     */
    public function onPreCreateOrder(OrderEvent $orderEvent)
    {
        try {
            $uploadFolder = $this->uploaderHandler->moveFiles($orderEvent->getUser()->getUserDirectory());
            $orderEvent->setUploadFolder($uploadFolder);


        } catch (FileSystemException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_FILE_SYSTEM_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }
	
	/**
	 * Save the order
	 *
	 * @param OrderEvent $orderEvent
	 *
	 * @throws ConnectionException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function onSaveOrder(OrderEvent $orderEvent)
    {
        try {
            $this->builder
                ->reset()
                ->setUser($orderEvent->getUser())
                ->setUploadFolder($orderEvent->getUploadFolder())
                ->setOrderStatus($orderEvent->getOrderStatus())
                ->setOrderType(Order::EMMOBILIER_TYPE)
                ->setOrderDeliveryTime()
                ->setPictures()
                ->setPromo()
                ->setOrderAmount()
            ;

            $orderEvent->setOrder($this->builder->getOrder());
        } catch (OrderException $e) {
            $orderEvent->setErrorType(OrderEvent::TYPE_ORDER_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * After Create the OrderCreation
     *
     * @param OrderEvent $orderEvent
     * @throws ConnectionException
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
        } catch (ConnectionException $e) {
            // Rollback the failed transaction attempt
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * After OrderCreation persist
     *
     * @param OrderEvent $orderEvent
     */
    public function onClearOrderData(OrderEvent $orderEvent)
    {
        $this->sessionManager->clear();
        $this->uploaderHandler->cleanUp($orderEvent->getUser()->getUserDirectory());
    }

    /**
     * Update the order status
     *
     * @param OrderEvent $orderEvent
     * @throws ConnectionException
     */
    public function onUpdateFreeOrder(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            // suspend auto-commit
            $this->em->getConnection()->beginTransaction();

            // generate the invoice
            $this->builder->setOrder($order)
                          ->setOrderInProduction()
                          ->generateOrderNumber()
                          ;

            // Try to flush and commit the transaction
            $this->em->flush();
            $this->em->getConnection()->commit();

            $orderEvent->setOrder($this->builder->getOrder());
        } catch (ConnectionException $e) {
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * Update the user wallet
     *
     * @param OrderEvent $orderEvent
     * @throws ConnectionException
     */
    public function onUpdateOrderAndUserWallet(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            $user = $orderEvent->getUser();
            // suspend auto-commit
            $this->em->getConnection()->beginTransaction();

            if ($order->getAmountIncludingTaxAfterReduction() > $user->getWallet()->getCurrentAmount()) {
                $orderEvent->setErrorMsg(sprintf('Error invoice amount %d > wallet amount %d', $order->getAmountIncludingTaxAfterReduction(), $user->getWallet()->getCurrentAmount()));
                $orderEvent->stopPropagation();
                return;
            }

            // generate the invoice
            $this->builder->setOrder($order)
                          ->setOrderInProduction()
                          ->generateOrderNumber()
                          ;

            $order = $this->builder->getOrder();
            $user->getWallet()->decreaseAmount($order->getAmountIncludingTaxAfterReduction());

            // Try to flush and commit the transaction
            $this->em->flush();
            $this->em->getConnection()->commit();

            $orderEvent->setOrder($order);
        } catch (ConnectionException $e) {
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * On Update the order of Monthly User
     *
     * @param OrderEvent $orderEvent
     * @throws ConnectionException
     */
    public function onUpdateUserMonthlyOrder(OrderEvent $orderEvent)
    {
      try {
          $order = $orderEvent->getOrder();
          // suspend auto-commit
          $this->em->getConnection()->beginTransaction();

          // generate the invoice
          $this->builder->setOrder($order)
                        ->setOrderInProduction()
                        ->generateOrderNumber()
                        ;

          // Try to flush and commit the transaction
          $this->em->flush();
          $this->em->getConnection()->commit();

          $orderEvent->setOrder($this->builder->getOrder());
      } catch (ConnectionException $e) {
          $this->em->getConnection()->rollback();
          $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
          $orderEvent->setErrorMsg($e->getMessage());
          $orderEvent->stopPropagation();
      }
    }

    /**
     * Pre Create the OrderCreation
     *
     * @param OrderEvent $orderEvent
     * @throws ConnectionException
     */
    public function onUpdateOrderAndValidateTransaction(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            $transaction = $orderEvent->getTransaction();

            // suspend auto-commit
            $this->em->getConnection()->beginTransaction();

            if (is_null($transaction) || !$transaction->isPaid()) {
                // set order status
                $this->builder->setOrder($order)
                              ->setOrderStatus(Order::ERROR_CB)
                              ;
            } else {
                // generate the invoice
                $this->builder->setOrder($order)
                            ->setTransaction($transaction)
                            ->setOrderInProduction()
                            ->generateOrderNumber()
                            ;
            }

            $order = $this->builder->getOrder();
            $this->em->flush();
            $this->em->getConnection()->commit();

            if ($order->getOrderStatus() == Order::ERROR_CB) {
              $orderEvent->stopPropagation();
            }
            // set the order to create the invoice
            $orderEvent->setOrder($order);
        } catch (ConnectionException $e) {
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }
}
