<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Entity\Order;
use App\Entity\OrderDeliveryTime;
use App\Entity\User;
use App\Entity\Transaction;

class OrderBuilder
{
    /**
     * @var PromoHandler
     */
    private $promoHandler;

    /**
     * @var OrderHandler
     */
    private $orderHandler;

    /**
     * @var Order
     */
    private $order;

    /**
     * Constructor
     *
     * @param PromoHandler $promoHandler
     * @param OrderHandler $orderHandler
     */
    public function __construct(PromoHandler $promoHandler, OrderHandler $orderHandler)
    {
        $this->promoHandler = $promoHandler;
        $this->orderHandler = $orderHandler;
    }

    /**
     * Create order object
     *
     * @return OrderBuilder
     */
    public function reset(): self
    {
        $this->order = new Order();
        $this->order->setSendEmail(false)
            ->setCreationDate(new \DateTime('now'));
        return $this;
    }

    /**
     * Set the generate the orderNumber
     *
     * @return OrderBuilder
     */
    public function generateOrderNumber(): self
    {
        if (!$this->order->getOrderNumber()) {
            $this->order->setOrderNumber($this->orderHandler->getTheLastOrderNumber());
        }

        return $this;
    }

    /**
     * Set the current user
     *
     * @param User $user
     * @return OrderBuilder
     */
    public function setUser(User $user): self
    {
        $this->order->setClient($user);

        return $this;
    }

    /**
     * Set the order transaction object
     *
     * @param Transaction|null $transaction
     * @return OrderBuilder
     */
    public function setTransaction(?Transaction $transaction): self
    {
        $transaction->setOrderTransaction($this->order);
        $this->order->setTransaction($transaction);

        return $this;
    }

    /**
     * Set the order in status
     *
     * @param string $status
     * @return OrderBuilder
     */
    public function setOrderStatus(string $status = Order::AWAITING_FOR_PAYMENT): self
    {
        $this->order->setOrderStatus($status);

        return $this;
    }

    /**
     * Set order type
     *
     * @param string $orderType
     * @return OrderBuilder
     */
    public function setOrderType(string $orderType): self
    {
        $this->order->setAppType($orderType);

        return $this;
    }

    /**
     * Set the order status in production
     *
     * @return OrderBuilder
     */
    public function setOrderInProduction(): self
    {
        $this->order->setOrderStatus(Order::IN_PRODUCTION);
        $this->order->setPaymentDate(new \DateTime('now'));

        return $this;
    }

    /**
     * Set the order in PENDING status
     *
     * @return OrderBuilder
     */
    public function pending(): self
    {
        $this->order->setOrderStatus(Order::PENDING);

        return $this;
    }

    /**
     * Set the order in AWAITING_FOR_COMPLETION status
     *
     * @return OrderBuilder
     */
    public function awaitingForCompletion(): self
    {
        $this->order->setOrderStatus(Order::AWAITING_FOR_COMPLETION);

        return $this;
    }
	
	/**
	 * Set the order time line
	 *
	 * @param OrderDeliveryTime|null $orderDeliveryTime
	 * @return OrderBuilder
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function setOrderDeliveryTime(?OrderDeliveryTime $orderDeliveryTime = null): self
    {
	    $this->orderHandler->initDeliveryTime();
	    
        if (is_null($orderDeliveryTime)) {
            $orderDeliveryTime = $this->orderHandler->getCurrentOrderDeliveryTime();
        }

        $this->order->setDeliveryTime($orderDeliveryTime);

        return $this;
    }

    /**
     * Set the pictures
     *
     * @param array $pictures
     * @return OrderBuilder
     */
    public function setPictures(array $pictures = []): self
    {
        if (empty($pictures)){
            $pictures = $this->orderHandler->createPicturesObjects($this->order);
        }else{
            $pictures = $this->orderHandler->createPicturesObjectsFromArray($this->order, $pictures);
        }

        $this->order->setPictures($pictures);
            
        $deliveryTime = null;

        foreach ($pictures as $picture){
            foreach ($picture->getPictureDetail()->toArray() as $pictureDetail){
                $prices = $pictureDetail->getRetouch()->getPricings()->toArray();
                if (count($prices) > 1){
                    return $this;
                } else {
                    if ($deliveryTime === null){
                        $deliveryTime = $prices[0]->getOrderDeliveryTime();
                    } else {
                        if ($prices[0]->getOrderDeliveryTime() !== $deliveryTime){
                            return $this;
                        }
                    }
                }
            }
        }
        $this->order->setDeliveryTime($deliveryTime);

        return $this;
    }

    /**
     * Upload Folder
     *
     * @param string $uploadFolder
     *
     * @return OrderBuilder
     */
    public function setUploadFolder(string $uploadFolder): self
    {
        $this->order->setUploadFolder($uploadFolder);

        return $this;
    }

    /**
     * Set the promo code
     *
     * @return OrderBuilder
     */
    public function setPromo(): self
    {
        $this->order->setPromotion($this->promoHandler->getCurrentPromo());

        return $this;
    }
	
	/**
	 * Generate the inovice for this order
	 *
	 * @return OrderBuilder
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
    public function setOrderAmount(): self
    {
        $this->order = $this->orderHandler->updateOrderPrice($this->order);

        return $this;
    }

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * Set order
     *
     * @param  Order $order
     * @return OrderBuilder
     */
    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }
}
