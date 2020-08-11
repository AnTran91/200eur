<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * @param User       $user
     *
     * @return Invoice[]
     */
    public function findByUser($user)
    {
        return $this->createQueryBuilder('i')
            ->join('i.order', 'o')
            ->join('o.client', 'u')
            ->where('u.id = :value')
            ->setParameter('value', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int $organization_id
     * @param string $appType
     *
     * @return Invoice|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findInvoiceMonthlyByDateAndOrganization(\DateTime $from, \DateTime $to, int $organization_id, string $appType): ?Invoice
    {
        return $this->createQueryBuilder('i')
                    ->join('i.organization', 'organization')
                    ->where(sprintf("i.type = '%s'", Invoice::MONTHLY_PER_ORGANIZATION))

                    ->andWhere('i.creationDate >= :from')
                    ->andWhere('i.creationDate < :to')
                    ->andWhere('organization.id = :organization_id')
                    ->andWhere('i.appType = :appType')
                    ->setParameter('appType', $appType)
                    ->setParameter('from', $from)
                    ->setParameter('to', $to->modify('+1 day'))
                    ->setParameter('organization_id', $organization_id)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                ;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int $client_id
     * @param string $appType
     *
     * @return Invoice|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findInvoiceMonthlyByDateAndClient(\DateTime $from, \DateTime $to, int $client_id, string $appType): ?Invoice
    {
        return $this->createQueryBuilder('i')
                    ->join('i.client', 'client')
                    ->where(sprintf("i.type = '%s'", Invoice::MONTHLY_PER_USER))
                    ->andWhere('i.appType = :appType')
                    ->andWhere('i.creationDate >= :from')
                    ->andWhere('i.creationDate < :to')
                    ->andWhere('client.id = :client_id')
                    ->setParameter('appType', $appType)
                    ->setParameter('from', $from)
                    ->setParameter('to', $to)
                    ->setParameter('client_id', $client_id)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                ;
    }

    /**
     * @param User|null $user
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $type
     *
     * @return Invoice[]
     */
    public function findByUserAndDate(?User $user, \DateTime $from, \DateTime $to, string $type = null)
    {
        $qb = $this->createQueryBuilder('i')
            ->Where('i.creationDate >= :from')
            ->andWhere('i.creationDate < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to->modify('+1 day'));

        if ($user) {
            $qb->leftJoin('i.client', 'u')->andWhere('u.id = :value')->setParameter('value', $user->getId());
        }

        if($type){
            $qb->AndWhere('i.type = :type')->setParameter('type', $type);
        } else {
            $qb->AndWhere('i.type != :type')->setParameter('type', Invoice::WALLET);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime   $from
     * @param \DateTime   $to
     *
     * @return Invoice[]
     */
    public function findByDate($from, $to)
    {
        // $fromTime = new \DateTime($year.'-'.$month.'-1');
        // $toTime = new \DateTime($fromTime->format('Y-m-d') . ' first day of next month');

        return $this->createQueryBuilder('i')
            // ->addSelect('o')
            // ->join('i.order', 'o')
            ->andWhere('i.creationDate >= :from')
            ->andWhere('i.creationDate < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to->modify('+1 day'))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get the last order And invoice Number
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastInvoiceNumber()
    {
        return $this->createQueryBuilder('i')
              ->select('Max(i.invoiceNumber) as lastNumber')
              ->getQuery()
              ->getOneOrNullResult()
        ;
    }
}
