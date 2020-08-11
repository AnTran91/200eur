<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
	
	/**
	 * @param $type
	 * @return mixed
	 */
    public function findByType($type)
    {
        return $this->createQueryBuilder('u')
            ->where('u.type = :value')->setParameter('value', $type)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
	
	/**
	 * @param int $id
	 * @return \Doctrine\ORM\Query
	 */
    public function getQueryFindByOrganization(int $id)
    {
        return $this->createQueryBuilder('u')
                  ->join('u.organization', 'o')
                  ->where('o.id = :id')
                  ->setParameter('id', $id)
                  ->getQuery()
        ;
    }

    /**
     * Find All by date
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByDate($from, $to)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u) as userNumber')
            ->where('u.lastLogin BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param string $apiToken
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByApiToken(string $apiToken): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.apiToken = :apiToken')
            ->setParameter('apiToken', $apiToken)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
	
	/**
	 * @param array $data
	 * @param bool $isSuperAdmin
	 * @return bool
	 */
    public function disableOrEnableUsersByIds(array $data, bool $isSuperAdmin = false): bool
    {
    	$success = true;
	    try {
		    // suspend auto-commit
		    $this->_em->beginTransaction();
		    foreach ($this->findBy(['id' => $data]) as $user) {
		    	if ($isSuperAdmin){
				    $user->setEnabled($user->isEnabled() ? false : true);
			    }else{
		    		if ($user->isSuperAdmin()){
		    		    continue;
				    }
				    $user->setEnabled($user->isEnabled() ? false : true);
			    }
		    }
		    // Flush and commit the transaction
		    $this->_em->flush();
		    $this->_em->commit();
	    } catch (\Exception $e) {
		    $this->_em->rollBack();
		    $success = false;
	    } finally {
	    	return $success;
	    }
    }
}
