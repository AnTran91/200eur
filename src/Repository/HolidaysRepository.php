<?php

namespace App\Repository;

use App\Entity\Holidays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Holidays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holidays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holidays[]    findAll()
 * @method Holidays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidaysRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Holidays::class);
    }

    /**
     * @return Holidays[] Returns an array of Holidays objects
     */
    public function findByMonthIterval(int $month = 1)
    {
      return $this->createQueryBuilder('h')
          ->andWhere('h.startDate BETWEEN :start AND :end ')
          ->setParameter('start', new \DateTime(sprintf('-%d month', $month)))
          ->setParameter('end', new \DateTime(sprintf('+%d month', $month)))
          ->orderBy('h.id', 'ASC')
          ->getQuery()
          ->getResult()
      ;
    }


   /**
    * @return Holidays[] Returns an array of Holidays objects
    */
    public function findByday($day)
    {
        return $this->createQueryBuilder('h')
            ->andWhere(':val BETWEEN h.startDate AND h.endDate ')
            ->setParameter('val', $day->format('Y-m-d'))
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
