<?php

namespace App\Repository;

use App\Entity\FieldRenovationChoices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FieldRenovationChoices|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldRenovationChoices|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldRenovationChoices[]    findAll()
 * @method FieldRenovationChoices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldRenovationChoicesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FieldRenovationChoices::class);
    }

//    /**
//     * @return FieldRenovationChoices[] Returns an array of FieldRenovationChoices objects
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
    public function findOneBySomeField($value): ?FieldRenovationChoices
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
