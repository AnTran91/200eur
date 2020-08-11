<?php

namespace App\Handlers\OrderHandlers;

use App\Entity as Entity;

class SessionManager extends OrderHandlerBase
{
    /**
     * Init delivery time
     *
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function initDeliveryTime(): void
    {
        if (!$this->sessionManager->has('deliveryTime')) {
          $this->sessionManager->set('deliveryTime',
              $this->em->getRepository(Entity\OrderDeliveryTime::class)
                        ->findTheDefaultField());
        }
    }

    /**
     * Return the current delivery time
     *
     * @return Entity\OrderDeliveryTime
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCurrentOrderDeliveryTime(): Entity\OrderDeliveryTime
    {
        $orderDeliveryTimeRepository = $this->em->getRepository(Entity\OrderDeliveryTime::class);

        if ($this->sessionManager->has('deliveryTime') && is_object($this->sessionManager->get('deliveryTime'))) {
            return $orderDeliveryTimeRepository->findOneBy(['id' => $this->sessionManager->get('deliveryTime')->getId()]);
        }

        return $orderDeliveryTimeRepository->findTheDefaultField();
    }
}
