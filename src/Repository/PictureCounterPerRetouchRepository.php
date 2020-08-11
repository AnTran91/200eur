<?php

namespace App\Repository;

use App\Entity\PictureCounterPerRetouch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PictureCounterPerRetouch|null find($id, $lockMode = null, $lockVersion = null)
 * @method PictureCounterPerRetouch|null findOneBy(array $criteria, array $orderBy = null)
 * @method PictureCounterPerRetouch[]    findAll()
 * @method PictureCounterPerRetouch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureCounterPerRetouchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PictureCounterPerRetouch::class);
    }

//    /**
//     * @return PictureCounterPerRetouch[] Returns an array of PictureCounterPerRetouch objects
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
    public function findOneBySomeField($value): ?PictureCounterPerRetouch
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
