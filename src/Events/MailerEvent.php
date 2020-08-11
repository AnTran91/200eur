<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;

use App\Entity\User;
use App\Entity\Order;

class MailerEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Order
     */
    private $order;
	
	/**
	 * Constructor
	 *
	 * @param User $user
	 * @param Order $order
	 */
    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }

    /**
    * @param $user
    */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
    * @return User
    */
    public function getUser(): User
    {
        return $this->user;
    }
	
	/**
	 * Get the cuurent OrderCreation
	 *
	 * @param Order $order
	 * @return void
	 */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * Get the cuurent OrderCreation
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }
}
