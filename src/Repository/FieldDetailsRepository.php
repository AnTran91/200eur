<?php

namespace App\Repository;

use App\Entity\FieldDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FieldDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldDetails[]    findAll()
 * @method FieldDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldDetailsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FieldDetails::class);
    }

//    /**
//     * @return FieldDetails[] Returns an array of FieldDetails objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FieldDetails
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
