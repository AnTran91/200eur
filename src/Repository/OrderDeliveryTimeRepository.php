<?php

namespace App\Repository;

use App\Entity\OrderDeliveryTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrderDeliveryTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderDeliveryTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderDeliveryTime[]    findAll()
 * @method OrderDeliveryTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderDeliveryTimeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderDeliveryTime::class);
    }

    /**
     * @return OrderDeliveryTime Returns an OrderDeliveryTime object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findTheDefaultField(): ?OrderDeliveryTime
    {
        return $this->createQueryBuilder('o')
            ->where('o.selectedByDefault = True')
            ->andWhere('o.appType = :appType')
            ->setParameter('appType', OrderDeliveryTime::EMMOBILIER_TYPE)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
