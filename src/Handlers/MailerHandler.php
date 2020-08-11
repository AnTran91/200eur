<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Entity\Order;
use App\Entity\User;

class MailerHandler
{
    private $mailer;
    private $twig;
    private $senderAddress;
	
	/**
	 * Construct
	 *
	 * @param \Swift_Mailer $mailer
	 * @param \Twig_Environment $twig
	 * @param $senderAddress
	 */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $senderAddress)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->senderAddress = $senderAddress;
    }

    /**
     * Send an email when the wallet reach the limit = 50 eurro
     *
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function WalletThresholdEvent(): array
    {
        $body = $this->twig->render('_shared_components/emails/wallet_threshold.html.twig');
        $title = "Solde Tirelire";

        return [$body, $title];
    }

    /**
     * Send an email when the images of the first deadline are ready
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function FirstDeadlineReadyEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/first_deadline_ready.html.twig', ['order' => $order]);
        $title = "Photos du 1er délais prêtes pour la commande ".$order->getOrderNumber();

        return [$body, $title];
    }

    /**
     * Send an email when the order is created
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function CreationEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/order_creation.html.twig', ['order' => $order]);
        $title = "Emmobilier-Création de la Commande ".$order->getOrderNumber();

        return [$body, $title];
    }

    /**
     * Send an email when recharge wallet
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function RechargeEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/recharge_wallet.html.twig', ['order' => $order]);
        $title = "Emmobilier-Création de la Commande ".$order->getOrderNumber();

        return [$body, $title];
    }

    /**
     * Send an email when the order is in production
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function PendingEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/order_pending.html.twig', ['order' => $order]);
        $title = "MISE EN ATTENTE de votre commande N°".$order->getorderNumber().".";

        return [$body, $title];
    }

    /**
     * Send an email when the order is in production
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function StopedEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/order_stoped.html.twig', ['order' => $order]);
        $title = "Commande ".$order->getOrderNumber()." mise en Arrêt";

        return [$body, $title];
    }

    /**
     * Send an email when the order is ready
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function ReadyEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/order_ready.html.twig', ['order' => $order]);
        $title = "Commande ".$order->getOrderNumber()." Prête";

        return [$body, $title];
    }

    /**
     * Send an email when the order is cancelled
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function CanceledEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/order_canceled.html.twig', ['order' => $order]);
        $title = "Commande ".$order->getorderNumber()." annulée!";

        return [$body, $title];
    }

    /**
     * Send an email when the order is cancelled
     *
     * @param Order $order
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function RefusedEvent(Order $order): array
    {
        $body = $this->twig->render('_shared_components/emails/order_refused.html.twig', ['order' => $order]);
        $title = "Commande ".$order->getorderNumber()." refusée!";

        return [$body, $title];
    }


    /**
     * Send an email when the order is created
     *
     * @param $to
     * @param string $event
     * @param Order|null $order
     * @return void
     */
    public function sendMail($to, $event = 'CreationEvent', ?Order $order = null): void
    {
        $msg_data = $this->$event($order);

        $message = (new \Swift_Message($msg_data[1]))
        ->setFrom($this->senderAddress)
        ->setTo($to)
        ->setBody($msg_data[0])
        ->addPart($msg_data[0], 'text/html');

        $this->mailer->send($message);
    }

    /**
     * Send an email when the order is created
     *
     * @param User $to
     * @param string $subject
     * @param string $content
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendSimpleMail(User $to, $subject, $content): void
    {
        $body = $this->twig->render('_shared_components/emails/simple_message.html.twig', ['content' => $content]);

        $message = (new \Swift_Message($subject))
        ->setFrom($this->senderAddress)
        ->setTo($to->getEmail())
        ->setBody($body)
        ->addPart($body, 'text/html');

        $this->mailer->send($message);
    }

}
