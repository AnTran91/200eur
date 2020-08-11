<?php

namespace App\Repository;

use App\Entity\PictureCounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PictureCounter|null find($id, $lockMode = null, $lockVersion = null)
 * @method PictureCounter|null findOneBy(array $criteria, array $orderBy = null)
 * @method PictureCounter[]    findAll()
 * @method PictureCounter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureCounterRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PictureCounter::class);
    }

//    /**
//     * @return PictureCounter[] Returns an array of PictureCounter objects
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
    public function findOneBySomeField($value): ?PictureCounter
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
