<?php

namespace App\Repository;

use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Picture::class);
    }

   /**
    * @return Query Returns an array of Picture objects
    */
    public function getQueryFindByOrder($orderId)
    {
        return $this->createQueryBuilder('p')
            ->join('p.order', 'o')
            ->andWhere('o.id = :order_id')
            ->setParameter('order_id', $orderId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Picture
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
