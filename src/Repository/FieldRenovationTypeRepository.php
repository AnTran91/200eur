<?php

namespace App\Repository;

use App\Entity\FieldRenovationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FieldRenovationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldRenovationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldRenovationType[]    findAll()
 * @method FieldRenovationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldRenovationTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FieldRenovationType::class);
    }

//    /**
//     * @return FieldRenovationType[] Returns an array of FieldRenovationType objects
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
    public function findOneBySomeField($value): ?FieldRenovationType
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
