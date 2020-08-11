<?php

namespace App\EventSubscriber;

use App\Entity\PictureDiscount;
use App\Entity\Promo;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Events\OrderEvent;
use App\Utils\Events;

use App\Handlers\InvoiceBuilder;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Invoice;

/**
 * @see \App\Events\OrderEvent class.
 */
final class InvoiceEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var InvoiceBuilder
     */
    private $builder;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor
     *
     * @param \App\Handlers\InvoiceBuilder          $builder
     * @param \Doctrine\ORM\EntityManagerInterface  $em
     */
    public function __construct(InvoiceBuilder $builder, EntityManagerInterface $em)
    {
        $this->builder = $builder;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          Events::ON_FREE_ORDER => array(
            array('onCreateOrdinaryInvoice', -10),
            array('onCreateOrganizationMonthlyInvoice', -20)
          ),
          Events::ON_PAY_ORDER_BY_WALLET => array(
            array('onCreateWalletInvoice', -10),
            array('onCreateOrganizationMonthlyInvoice', -20)
          ),
          Events::ON_PAY_ORDER_BY_TRANSACTION => array(
            array('onCreateOrdinaryInvoice', -10),
            array('onCreateOrganizationMonthlyInvoice', -20)
          ),
          Events::ON_SAVE_MONTHLY_ORDER => array(
            array('onCreateUserMonthlyInvoice', -10),
            array('onCreateOrganizationMonthlyInvoice', -20)
          ),
          Events::ON_CREATE_IMMOSQUARE_ORDER => array(
            array('onCreateUserMonthlyInvoice', -20)
          ),
          Events::ON_ADD_ADDITIONAL_INVOICE => array(
            array('onCreateAdditionalInvoice', -10)
          ),
          Events::ON_RECHARGE_TO_WALLET => array(
            array('onCreateRechargeInvoice', -10)
          )
        ];
    }

    /**
     * Update the wallet
     * @param OrderEvent $orderEvent
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onCreateRechargeInvoice(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            // suspend auto-commit
            $this->em->getConnection()->beginTransaction();

            // generate the invoice
            $this->builder
              ->reset($order->findOneInvoiceByType([Invoice::RECHARGE]))
              ->generateRechargeInvoice($order)
            ;

            // Try to flush and commit the transaction
            $this->em->persist($order);
            $this->em->flush();
            $this->em->getConnection()->commit();

            $orderEvent->setOrder($order);
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback(); 
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * Update the order status
     * @param OrderEvent $orderEvent
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onCreateOrdinaryInvoice(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            // suspend auto-commit
            $this->em->getConnection()->beginTransaction();

            // generate the invoice
            $this->builder
              ->reset($order->findOneInvoiceByType([Invoice::ORDINARY]))
              ->generateInvoice($order)
            ;

            // Try to flush and commit the transaction
            $this->em->flush();
            $this->em->getConnection()->commit();

            $orderEvent->setOrder($order);
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback(); 
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * Update the order status
     * @param OrderEvent $orderEvent
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onCreateWalletInvoice(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            // suspend auto-commit
            $this->em->getConnection()->beginTransaction();

            // generate the invoice
            $this->builder
              ->reset($order->findOneInvoiceByType([Invoice::WALLET]))
              ->generateTheWalletInvoice($order)
            ;

            // Try to flush and commit the transaction
            $this->em->flush();
            $this->em->getConnection()->commit();

            $orderEvent->setOrder($order);
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * On create invoice pdf After create the Additional invoice
     * @param OrderEvent $orderEvent
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onCreateAdditionalInvoice(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            // generate the invoice
            $this->builder
              ->reset($order->findOneInvoiceByType([Invoice::ADDITIONAL]))
              ->generateTheAdditionalInvoice($order)
            ;

            $this->em->flush();

            $orderEvent->setOrder($order);
        } catch (\Exception $e) {
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
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onCreateUserMonthlyInvoice(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();

            $form = new \DateTime(date('Y-m-01', strtotime(date('Y-m-d'))));
            $to =  new \DateTime(date('Y-m-t', strtotime(date('Y-m-d'))));

            $invoice = $this->em
              ->getRepository(Invoice::class)
              ->findInvoiceMonthlyByDateAndClient($form, $to, $order->getClient()->getId(), $order->getAppType())
            ;

            // generate the invoice
            $this->builder
              ->reset($invoice)
              ->generateUserMonthlyInovice($order, $order->getClient())
            ;

            $this->em->flush();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }

    /**
     * On create mothly invoice
     * @param OrderEvent $orderEvent
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onCreateOrganizationMonthlyInvoice(OrderEvent $orderEvent)
    {
        try {
            $order = $orderEvent->getOrder();
            $promo = $order->getPromotion();

            // if (is_null($promo) || is_null($promo->getOrganization()) || !$promo instanceof PictureDiscount || $promo->getPromoType() == Promo::ASSIGN_TO_ALL_CUSTOMERS || $promo->getPromoType() == Promo::ASSIGN_TO_SPECIFIC_CUSTOMERS) {
            //     return;
            // }

            if (is_null($promo) || is_null($promo->getOrganization()) || !$promo instanceof PictureDiscount || $promo->getPromoType() == Promo::ASSIGN_TO_ALL_CUSTOMERS || $promo->getPromoType() == Promo::ASSIGN_TO_SPECIFIC_CUSTOMERS || !$order->getClient()->getOrganization()->getActiveMonthlyInvoice()) {
                return;
            }

            $form = new \DateTime(date('Y-m-01', strtotime(date('Y-m-d'))));
            $to =  new \DateTime(date('Y-m-t', strtotime(date('Y-m-d'))));

            $organization = $order->getClient()->getOrganization();
            if (method_exists($order->getClient()->getOrganization(), 'getNetwork') && !is_null($order->getClient()->getOrganization()->getNetwork())) {
                $organization = $order->getClient()->getOrganization()->getNetwork();
            }

            $invoice = $this->em->getRepository(Invoice::class)
                        ->findInvoiceMonthlyByDateAndOrganization($form, $to, $organization->getId(), $order->getAppType());

            // generate the invoice
            $this->builder
              ->reset($invoice)
              ->generateOrganizationMonthlyInvoice($order, $organization)
            ;

            $this->em->flush();
            // set the order to create the invoice
            $orderEvent->setOrder($order);
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            $orderEvent->setErrorType(OrderEvent::TYPE_DATABASE_ERROR);
            $orderEvent->setErrorMsg($e->getMessage());
            $orderEvent->stopPropagation();
        }
    }
}
