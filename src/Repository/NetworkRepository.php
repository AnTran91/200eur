<?php

namespace App\Repository;

use App\Entity\Network;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Network|null find($id, $lockMode = null, $lockVersion = null)
 * @method Network|null findOneBy(array $criteria, array $orderBy = null)
 * @method Network[]    findAll()
 * @method Network[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NetworkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Network::class);
    }

    /**
     * @param string[] $criteria format: array('user' => <user_id>, 'name' => <name>)
     */
    public function findUniqueCriteria(array $criteria)
    {
        // would use findOneBy() but Symfony expects a Countable object
        return $this->_em->getRepository(Organization::class)->findBy($criteria);
    }

//    /**
//     * @return Network[] Returns an array of Network objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findByRegistrationCode($value): ?Network
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.registrationCode = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Network
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
