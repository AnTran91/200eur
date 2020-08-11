<?php

namespace App\Repository;

use App\Entity\PictureDiscountPerRetouch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PictureDiscountPerRetouch|null find($id, $lockMode = null, $lockVersion = null)
 * @method PictureDiscountPerRetouch|null findOneBy(array $criteria, array $orderBy = null)
 * @method PictureDiscountPerRetouch[]    findAll()
 * @method PictureDiscountPerRetouch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureDiscountPerRetouchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PictureDiscountPerRetouch::class);
    }

//    /**
//     * @return PictureDiscountPerRetouch[] Returns an array of PictureDiscountPerRetouch objects
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
    public function findOneBySomeField($value): ?PictureDiscountPerRetouch
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
