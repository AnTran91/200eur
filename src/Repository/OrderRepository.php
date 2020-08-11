<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, Order::class);
	}
	
	/**
	 * Find Order by Id OR order number used in the immosquare API
	 *
	 * @param $orderId
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByIdOrOrderNumber($orderId){
		
		return $this->createQueryBuilder('o')
			->where('o.orderNumber = :order_id')
			->setParameter('order_id', $orderId)
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * Find Orders By Promo Code
	 *
	 * @param string $promoCode
	 *
	 * @return array
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByPromoCode(string $promoCode)
	{
		return $this->createQueryBuilder('o')
			->select('count(o) as useNumber, count(i) as imageNumber')
			->join('o.promotion', 'p')
			->join('o.pictures', 'i')
			->where('p.promoCode = :codePromo')
			->setParameter('codePromo', $promoCode)
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * @param string $invoice_id
	 * @return \Doctrine\ORM\Query
	 */
	public function getQueryFindByInvoice(string $invoice_id)
	{
		return $this->createQueryBuilder('o')
			->addSelect('invoice')
			->join('o.invoices', 'invoice')
			->where('invoice.id = :id')
			// ->setParameter('order_status', 'order.status.declined_by_production')
			->setParameter('id', $invoice_id)
			->getQuery();
	}
	
	/**
	 * @param string $id
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findOrderByID(string $id)
	{
		return $this->createQueryBuilder('o')
			->addSelect('pictures', 'pictureDetail', 'retouch', 'deliveryTime', 'pricings', 'client', 'promotion')
			->leftJoin('o.promotion', 'promotion')
			->leftJoin('o.deliveryTime', 'deliveryTime')
			->innerJoin('o.client', 'client')
			->leftJoin('o.pictures', 'pictures')
			->leftJoin('pictures.pictureDetail', 'pictureDetail')
			->leftJoin('pictureDetail.retouch', 'retouch')
			->leftJoin('retouch.pricings', 'pricings')
			->where('o.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * Find Orders By User and status
	 *
	 * @param User $user
	 * @param null|string $status
	 * @return \Doctrine\ORM\Query
	 */
	public function findAllByUserAndStatus(User $user, ?string $status)
	{
		return $this->createQueryBuilder('o')
			->innerJoin('o.pictures', 'p')
			->leftJoin('o.transaction', 't')
			->leftJoin('o.promotion', 'pr')
			->leftJoin('o.invoices', 'i')
			->addSelect('t', 'i', 'p', 'pr')
			->where('o.orderStatus like :status')
			->AndWhere('o.appType = :appType')
			->AndWhere('o.client = :user')
			->AndWhere('o.deletedAt is NULL')
			->setParameter('user', $user)
			->setParameter(':appType', Order::EMMOBILIER_TYPE)
			->setParameter('status', '%' . $status . '%')
			->orderBy('o.orderNumber', 'DESC')
			->addOrderBy('o.id', 'DESC')
			->getQuery();
	}

	/**
	 * Find Orders Bank Supply
	 *
	 * @param User $user
	 * @param null|string $status
	 * @return \Doctrine\ORM\Query
	 */
	public function findAllOrderByUserAndStatus(User $user, ?string $status)
	{
		return $this->createQueryBuilder('o')
			->leftJoin('o.transaction', 't')
			->leftJoin('o.invoices', 'i')
			->addSelect('t', 'i')
			->where('o.orderStatus like :status')
			->AndWhere('o.appType = :appType')
			->AndWhere('o.client = :user')
			->AndWhere('o.deletedAt is NULL')
			->setParameter('user', $user)
			->setParameter(':appType', Order::EMMOBILIER_TYPE)
			->setParameter('status', '%' . $status . '%')
			->orderBy('o.orderNumber', 'DESC')
			->addOrderBy('o.id', 'DESC')
			->getQuery();
	}
	
	/**
	 * count the useNumber & userImageNumber for a given promo
	 *
	 * @param int $promoId
	 *
	 * @return array (useNumber, userImageNumber)
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findTheUseNumberAndImageNumberByPromo(int $promoId)
	{
		return $this->createQueryBuilder('o')
			->select('count(o) as useNumber', 'count(pd) as imageNumber')
			->join('o.promotion', 'p')
			->innerJoin('o.pictures', 'pi')
			->leftJoin('pi.pictureDetails', 'pd')
			->where('p.id = :promo_id')
			->setParameter('promo_id', $promoId)
			->andWhere('o.paymentDate IS NOT NULL')
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * count the userUseNumber & userImageNumber for a given User and a given promo
	 *
	 * @param int $promoId
	 * @param int $userId
	 *
	 * @return array (userUseNumber, userImageNumber)
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findTheUseNumberByPromoAndUser(int $promoId, int $userId)
	{
		return $this->createQueryBuilder('o')
			->select('count(o) as userUseNumber', 'count(pd) as userImageNumber')
			->join('o.client', 'u')
			->join('o.promotion', 'p')
			->innerJoin('o.pictures', 'pi')
			->leftJoin('pi.pictureDetails', 'pd')
			->where('p.id = :promo_id')
			->setParameter('promo_id', $promoId)
			->andWhere('u.id = :user_id')
			->setParameter('user_id', $userId)
			->andWhere('o.paymentDate IS NOT NULL')
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * count the userUseNumber & userImageNumber for a given User and a given promo
	 *
	 * @param int $promoId
	 * @param int $userId
	 *
	 * @return array (userUseNumber, userImageNumber)
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findTheImageNumberByPromoAndUser(int $promoId, int $userId)
	{
		return $this->createQueryBuilder('o')
			->select('count(o) as userUseNumber', 'count(pd) as userImageNumber')
			->join('o.client', 'u')
			->join('o.promotion', 'p')
			->innerJoin('o.pictures', 'pi')
			->leftJoin('pi.pictureDetails', 'pd')
			->where('p.id = :promo_id')
			->setParameter('promo_id', $promoId)
			->andWhere('u.id = :user_id')
			->setParameter('user_id', $userId)
			->andWhere('o.paymentDate IS NOT NULL')
			->groupBy('pd')
			->having('imageNumber <= p.imageLimitPerUser')
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * count the orders for a given user
	 *
	 * @param int $userId
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function countOrdersByUser(int $userId)
	{
		return $this->createQueryBuilder('o')
			->select('count(o) as orderCount')
			->join('o.client', 'u')
			->where('u.id = :userId')
			->setParameter('userId', $userId)
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * Get the last order And invoice Number
	 *
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findLastOrderNumber()
	{
		return $this->createQueryBuilder('o')
			->select('Max(o.orderNumber) as lastNumber')
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * find last order
	 *
	 * @param int $userId
	 *
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findUserLastOrder(int $userId)
	{
		return $this->createQueryBuilder('o')
			->select('o.orderNumber as number')
			->join('o.client', 'u')
			->where('u.id = :userId')
			->setParameter('userId', $userId)
			->orderBy('number', 'DESC')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * Find Orders By date and status
	 *
	 * @param string $status
	 * @param string $date
	 * @param string $appType
	 *
	 * @return array
	 */
	public function findOrdersByStatusAndDate(?string $status, string $date, string $appType)
	{
		return $this->createQueryBuilder('o')
			->where('o.orderStatus = :status')
			->andWhere('o.deliveranceDate = :date')
			->andWhere('o.appType = :appType')
			->setParameter('appType', $appType)
			->setParameter('status', $status)
			->setParameter('date', $date)
			->orderBy('o.orderNumber', 'DESC')
			->getQuery()
			->getResult();
	}
	
	/**
	 * Find Orders By date and status
	 *
	 * @param string $status
	 * @param string $appType
	 *
	 * @return array
	 */
	public function findOrdersByStatus(string $status, string $appType)
	{
		return $this->createQueryBuilder('o')
			->where('o.orderStatus like :status')
			->setParameter('status', '%' . $status . '%')
			->andWhere('o.appType = :appType')
			->setParameter('appType', $appType)
			->orderBy('o.orderNumber', 'DESC')
			->getQuery()
			->getResult();
	}
	
	/**
	 * Find Orders By date and status
	 *
	 * @param string $status
	 * @param string $startDate
	 * @param string $endDate
	 *
	 * @param string $appType
	 * @return array
	 */
	public function findOrdersByStatusAndDateInterval(string $status, $startDate, $endDate, string $appType)
	{
		return $this->createQueryBuilder('o')
			->where('o.orderStatus like :status')
			->andWhere('o.deliveranceDate >= :startDate')
			->andWhere('o.deliveranceDate <= :endDate')
			->andWhere('o.appType = :appType')
			->setParameter('appType', $appType)
			->setParameter('status', '%' . $status . '%')
			->setParameter('startDate', $startDate)
			->setParameter('endDate', $endDate)
			->orderBy('o.orderNumber', 'DESC')
			->getQuery()
			->getResult();
	}
	
	/**
	 * Find Orders that have no invoice
	 *
	 * @return array
	 */
	public function findByEmptyInvoice()
	{
		return $this->createQueryBuilder('o')
			->where('o.invoices is NULL')
			->orderBy('o.orderNumber', 'DESC')
			->getQuery()
			->getResult();
	}
	
	/**
	 * @param \DateTime $from
	 * @param \DateTime $to
	 *
	 * @param string $appType
	 * @return Order[]
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByDateInterval(\DateTime $from, \DateTime $to, string $appType)
	{
		return $this->createQueryBuilder('t')
			->select('sum(t.totalAmount) as profit')
			->where('t.paymentDate BETWEEN :monday AND :sunday')
			->andWhere('t.appType = :appType')
			->setParameter('appType', $appType)
			->setParameter('monday', $from)
			->setParameter('sunday', $to)
			->getQuery()
			->setMaxResults(1)
			->getOneOrNullResult();
	}

	/**
	 * @param \DateTime $from
	 * @param \DateTime $to
	 *
	 * @param string $appType
	 * @return Order[]
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByDateIntervalWithoutCanceled(\DateTime $from, \DateTime $to, string $appType)
	{
		return $this->createQueryBuilder('t')
			->select('sum(t.totalAmount) as profit')
			->where('t.paymentDate BETWEEN :monday AND :sunday')
			->andWhere('t.appType = :appType')
			->andWhere('t.orderStatus != :orderStatus')
			->setParameter('appType', $appType)
			->setParameter('monday', $from)
			->setParameter('sunday', $to)
			->setParameter('orderStatus', Order::DECLINED_BY_PRODUCTION)
			->getQuery()
			->setMaxResults(1)
			->getOneOrNullResult();
	}
	
	/**
	 * @param \DateTime $from
	 * @param \DateTime $to
	 * @param string $appType
	 *
	 * @return Order[]
	 */
	public function findAllGroupedByDate($from, $to, string $appType)
	{
		return $this->createQueryBuilder('t')
			->select('sum(t.totalAmount) as profit', 't.paymentDate as date')
			->where('t.paymentDate BETWEEN :monday AND :sunday')
			->andWhere('t.appType = :appType')
			->setParameter('appType', $appType)
			->setParameter('monday', $from)
			->setParameter('sunday', $to)
			->groupBy('date')
			->orderBy('date', 'DESC')
			->getQuery()
			->setMaxResults(500)
			->getResult();
	}
	
	/**
	 * @param string $appType
	 *
	 * @return mixed
	 */
	public function findAllGroupedByStatus(string $appType)
	{
		return $this->createQueryBuilder('o')
			->select('count(o) as countOrder', 'o.orderStatus as status')
			->andWhere('o.appType = :appType')
			->setParameter('appType', $appType)
			->groupBy('status')
			->getQuery()
			->getResult();
	}
	
	/**
	 * @param array $data
	 * @param bool $delete
	 * @return bool
	 */
	public function removeOrUndoOrdersByIds(array $data, bool $delete = true): bool
	{
		$success = true;
		try {
			$this->_em->getFilters()->disable('soft_deleteable');
			
			$this->_em->beginTransaction();
			foreach ($this->findBy(['id' => $data]) as $order) {
				if ($delete){
					$this->_em->remove($order);
				}else{
					$order->setDeletedAt(null);
				}
			}
			// Flush and commit the transaction
			$this->_em->flush();
			$this->_em->commit();
			
		} catch (\Throwable $e) {
			// Rollback the failed transaction attempt
			$this->_em->rollback();
			$success = false;
		} finally {
			return $success;
		}
	}
}
