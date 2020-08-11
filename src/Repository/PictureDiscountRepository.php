<?php

namespace App\Repository;

use App\Entity\PictureDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PictureDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method PictureDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method PictureDiscount[]    findAll()
 * @method PictureDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureDiscountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PictureDiscount::class);
    }

//    /**
//     * @return PictureDiscount[] Returns an array of PictureDiscount objects
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
    public function findOneBySomeField($value): ?PictureDiscount
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
