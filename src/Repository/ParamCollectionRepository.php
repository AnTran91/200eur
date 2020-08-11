<?php

namespace App\Repository;

use App\Entity\ParamCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ParamCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParamCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParamCollection[]    findAll()
 * @method ParamCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParamCollectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ParamCollection::class);
    }

//    /**
//     * @return ParamCollection[] Returns an array of ParamCollection objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ParamCollection
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
