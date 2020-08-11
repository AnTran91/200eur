<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Handlers\OrderHandlers as OrderHandlers;
use App\Entity as Entity;

class OrderHandler
{
    /**
     * @var OrderHandlers\SessionManager
     */
    private $sessionManager;

    /**
     * @var OrderHandlers\RevertToBackupOrder
     */
    private $backupOrder;

    /**
     * @var OrderHandlers\PictureManager
     */
    private $pictureManager;

    /**
     * @var OrderHandlers\OrderManager
     */
    private $orderManager;

    /**
     * @var OrderHandlers\ImmosquareManager
     */
    private $immosquareManager;

    /**
     * @var OrderHandlers\OrderFormatter
     */
    private $orderFormatter;

    /**
     * Constructor
     *
     * @param OrderHandlers\OrderFormatter          $orderFormatter
     * @param OrderHandlers\SessionManager          $sessionManager
     * @param OrderHandlers\RevertToBackupOrder     $backupOrder
     * @param OrderHandlers\PictureManager          $pictureManager
     * @param OrderHandlers\OrderManager            $orderManager
     * @param OrderHandlers\ImmosquareManager       $immosquareManager
     */
    public function __construct(OrderHandlers\OrderFormatter $orderFormatter, OrderHandlers\SessionManager $sessionManager, OrderHandlers\RevertToBackupOrder $backupOrder, OrderHandlers\PictureManager $pictureManager, OrderHandlers\OrderManager $orderManager, OrderHandlers\ImmosquareManager $immosquareManager)
    {
        $this->sessionManager = $sessionManager;
        $this->backupOrder = $backupOrder;
        $this->pictureManager = $pictureManager;
        $this->orderManager = $orderManager;
        $this->immosquareManager = $immosquareManager;
        $this->orderFormatter = $orderFormatter;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function initDeliveryTime(): void
    {
        $this->sessionManager->initDeliveryTime();
    }

    /**
     * @return Entity\OrderDeliveryTime
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCurrentOrderDeliveryTime(): Entity\OrderDeliveryTime
    {
        return $this->sessionManager->getCurrentOrderDeliveryTime();
    }

    /**
     * @param Entity\Order $order
     * @return Entity\Order
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function revertOrder(Entity\Order $order): Entity\Order
    {
        return $this->backupOrder->revertOrder($order);
    }

    /**
     * @param string $userDirectory
     * @return bool
     */
    public function validate(string $userDirectory): bool
    {
        return $this->pictureManager->validate($userDirectory);
    }

    /**
     * @param Entity\Order $order
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function createPicturesObjects(Entity\Order $order): \Doctrine\Common\Collections\ArrayCollection
    {
        return $this->pictureManager->createPicturesObjects($order);
    }

    /**
     * @param Entity\Order $order
     * @return array
     */
    public function formatCreatedOrder (Entity\Order $order): array
    {
        return $this->immosquareManager->formatCreatedOrder($order);
    }

    public function formatReadyOrder(Entity\Order $order): array
    {
        return $this->immosquareManager->formatReadyOrder($order);
    }

    /**
     * @param Entity\Order $order
     * @param array $uploadedFiles
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function createPicturesObjectsFromArray(Entity\Order $order, array $uploadedFiles): \Doctrine\Common\Collections\ArrayCollection
    {
        return $this->immosquareManager->createPicturesObjectsFromArray($order, $uploadedFiles);
    }

    /**
     * @param Entity\User $user
     * @param array $data
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function saveAndDisplayTemporaryOrder(Entity\User $user, array $data = array()): array
    {
        return $this->orderFormatter->saveAndDisplayTemporaryOrder($user, $data);
    }

    /**
     * @param Entity\Order $order
     * @return array
     */
    public function getFormattedOrder(Entity\Order $order): array
    {
        return $this->orderFormatter->getFormattedOrder($order);
    }

    /**
     * @param Entity\Order $order
     * @param array|null $data
     *
     * @return Entity\Order
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function updateOrderPrice(Entity\Order $order, ?array $data = null): Entity\Order
    {
        return $this->orderManager->updateOrderPrice($order, $data);
    }

    /**
     * @param Entity\Order $order
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function updateOrderStatus(Entity\Order $order): void
    {
        $this->orderManager->updateOrderStatus($order);
    }

    /**
     * @param array $retouchObjects
     * @param array $validParams
     * @param Entity\Picture|null $picture
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function editAndUpdateOrder(array $retouchObjects, array $validParams, Entity\Picture $picture = null): void
    {
        $this->orderManager->editAndUpdateOrder($retouchObjects, $validParams, $picture);
    }

    /**
     * @param Entity\User $user
     * @return float
     */
    public function calculateTax(Entity\User $user): float
    {
        return $this->orderManager->calculateTax($user);
    }
	
	/**
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function getTheLastOrderNumber(): int
    {
        return $this->orderManager->getTheLastOrderNumber();
    }
	
	/**
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function getTheLastInvoiceNumber(): int
    {
        return $this->orderManager->getTheLastInvoiceNumber();
    }

    /**
     * @param Entity\User $user
     * @return bool
     */
    public function doPicturesHasMultiplePrices(Entity\User $user): bool
    {
        return $this->orderManager->doPicturesHasMultiplePrices($user);
    }
}
