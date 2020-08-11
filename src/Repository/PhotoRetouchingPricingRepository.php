<?php

namespace App\Repository;

use App\Entity\PhotoRetouchingPricing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PhotoRetouchingPricing|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoRetouchingPricing|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoRetouchingPricing[]    findAll()
 * @method PhotoRetouchingPricing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRetouchingPricingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PhotoRetouchingPricing::class);
    }

//    /**
//     * @return PhotoRetouchingPricing[] Returns an array of PhotoRetouchingPricing objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhotoRetouchingPricing
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
