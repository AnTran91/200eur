<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

   /**
    * @return Transaction[] Returns an array of Transaction objects
    */
    public function findByUser($user)
    {
        return $this->createQueryBuilder('t')
            ->join('t.wallet', 'w')
            ->andWhere('w.client = :user')
            ->setParameter('user', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * get the last Transaction Number
     */
    public function findLastTransactionNumber()
    {
        return $this->createQueryBuilder('t')
              ->select('t.transactionNumber as lastTransactionNumber')
              ->orderBy('t.id', 'DESC')
              ->setMaxResults(1)
              ->getQuery()
              ->getOneOrNullResult()
        ;
    }
}
