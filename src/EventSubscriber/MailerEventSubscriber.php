<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Entity\Order;
use App\Utils\Events;

use App\Events\MailerEvent;
use App\Events\OrderEvent;

use Doctrine\ORM\EntityManagerInterface;

use App\Handlers\MailerHandler;

/**
 * @see \App\Events\MailerEvent class.
 * @see \App\Events\OrderEvent class.
 */
final class MailerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerHandler
     */
    private $mailerHandler;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(MailerHandler $mailerHandler, EntityManagerInterface $em)
    {
        $this->mailerHandler = $mailerHandler;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          // These event trigger on order creation
          Events::ON_FREE_ORDER => array(
            array('onCreateMailer', -40)
          ),
          Events::ON_PAY_ORDER_BY_WALLET => array(
            array('onPayWithWallet', -40)
            // array('onCreateMailer', -50)
          ),
          Events::ON_PAY_ORDER_BY_TRANSACTION => array(
            array('onCreateMailer', -40)
          ),
          Events::ON_SAVE_MONTHLY_ORDER => array(
            array('onCreateMailer', -40),
          ),
          Events::ON_RECHARGE_TO_WALLET => array(
            array('onCreateRechargeMailer', -40),
          ),

          // These event trigger in the edit page in admin
          Order::SEND_TO_CLIENT => array(
            array('onFinishedMailer', -40),
            array('emailSended', -40)
          ),
          Order::AWAITING_FOR_CLIENT_RESPONSE => array(
            array('onPendingMailer', -40),
            array('emailSended', -40)
          ),
          Order::DELIVERY_SHORT_TIME_READY => array(
            array('onFirstDeadlineReadyMailer', -40),
            array('emailSended', -40)
          ),
          Order::DECLINED_BY_PRODUCTION => array(
            array('onRefusingMailer', -40),
            array('emailSended', -40)
          ),
        ];
    }

    /**
     * After order creation
     * @param OrderEvent $orderEvent
     */
    public function onCreateMailer(OrderEvent $orderEvent)
    {
        $this->mailerHandler->sendMail($orderEvent->getUser()->getUsername(), 'CreationEvent', $orderEvent->getOrder());
    }

    /**
     * After recharge wallet
     * @param OrderEvent $orderEvent
     */
    public function onCreateRechargeMailer(OrderEvent $orderEvent)
    {
        $this->mailerHandler->sendMail($orderEvent->getUser()->getUsername(), 'RechargeEvent', $orderEvent->getOrder());
    }

    /**
     * After payment with wallet and the amount is less than 50
     * @param OrderEvent $orderEvent
     */
    public function onPayWithWallet(OrderEvent $orderEvent)
    {
        if ($orderEvent->getUser()->getWallet()->getCurrentAmount() < 50) {
            $this->mailerHandler->sendMail($orderEvent->getUser()->getUsername(), 'WalletThresholdEvent');
        }

        $this->mailerHandler->sendMail($orderEvent->getUser()->getUsername(), 'CreationEvent', $orderEvent->getOrder());
    }


    /**
     * When order is refused
     * @param MailerEvent $mailerEvent
     */
    public function onRefusingMailer(MailerEvent $mailerEvent)
    {
        $this->mailerHandler->sendMail($mailerEvent->getUser()->getUsername(), 'RefusedEvent', $mailerEvent->getOrder());
    }

    /**
     * When order is stoped
     * @param MailerEvent $mailerEvent
     */
    public function onStopMailer(MailerEvent $mailerEvent)
    {
        $this->mailerHandler->sendMail($mailerEvent->getUser()->getUsername(), 'StopedEvent', $mailerEvent->getOrder());
    }

    /**
     * When order is cancel
     * @param MailerEvent $mailerEvent
     */
    public function onCancelMailer(MailerEvent $mailerEvent)
    {
        $this->mailerHandler->sendMail($mailerEvent->getUser()->getUsername(), 'CanceledEvent', $mailerEvent->getOrder());
    }

    /**
     * When order is first deadline ready
     * @param MailerEvent $mailerEvent
     */
    public function onFirstDeadlineReadyMailer(MailerEvent $mailerEvent)
    {
        $this->mailerHandler->sendMail($mailerEvent->getUser()->getUsername(), 'FirstDeadlineReadyEvent', $mailerEvent->getOrder());
    }

    /**
     * When order is finished
     * @param MailerEvent $mailerEvent
     */
    public function onFinishedMailer(MailerEvent $mailerEvent)
    {
        $this->mailerHandler->sendMail($mailerEvent->getUser()->getUsername(), 'ReadyEvent', $mailerEvent->getOrder());
    }

    /**
     * When order in pending
     * @param MailerEvent $mailerEvent
     */
    public function onPendingMailer(MailerEvent $mailerEvent)
    {
        $this->mailerHandler->sendMail($mailerEvent->getUser()->getUsername(), 'PendingEvent', $mailerEvent->getOrder());
    }

    /**
     * Update order sended
     * @param MailerEvent $mailerEvent
     */
    public function emailSended(MailerEvent $mailerEvent)
    {
        $mailerEvent->getOrder()->setSendEmail(true)->setDeliveranceDate(new \DateTime());
        $this->em->flush();
    }
}
