<?php

namespace App\Repository;

use App\Entity\Agency;
use App\Entity\Organization;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Agency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agency[]    findAll()
 * @method Agency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgencyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Agency::class);
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
//     * @return Agency[] Returns an array of Agency objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findByRegistrationCode($value): ?Agency
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.registrationCode = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
