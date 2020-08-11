<?php

namespace App\Repository;

use App\Entity\Promo;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Promo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promo[]    findAll()
 * @method Promo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Promo::class);
    }
	
	/**
	 * count the useNumber & userImageNumber for a given promo
	 *
	 * @param int $promoId
	 *
	 * @return array (useNumber, userImageNumber)
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function findTheUseNumberByPromo(int $promoId)
    {
        return $this->createQueryBuilder('p')
            ->select('count(o) as useNumber')
            ->join('p.orders', 'o')
            ->where('p.id = :promo_id')
            ->setParameter('promo_id', $promoId)
            ->andWhere('o.paymentDate IS NOT NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
	
	/**
	 * count the useNumber & userImageNumber for a given promo
	 *
	 * @param int $promoId
	 *
	 * @param int $userId
	 * @return array (useNumber, userImageNumber)
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function findTheUseNumberByPromoAndUser(int $promoId, int $userId)
    {
        return $this->createQueryBuilder('p')
            ->select('count(o) as useNumber')
            ->join('p.orders', 'o')
            ->join('o.client', 'c')
            ->where('p.id = :promo_id')
            ->setParameter('promo_id', $promoId)
            ->andWhere('o.paymentDate IS NOT NULL')
            ->AndWhere('c.id = :user_id')
            ->setParameter('user_id', $userId)
            ->andWhere('o.paymentDate IS NOT NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
	
	/**
	 * count the useNumber & userImageNumber for a given promo
	 *
	 * @param int $promoId
	 * @param int $userId
	 *
	 * @return array (useNumber, userImageNumber)
	 */
    public function findThePicturesNumberByPromoAndUserGroupedByRetouch(int $promoId, int $userId)
    {
        return $this->createQueryBuilder('p')
            ->select('count(pd) as image_numbers', 're.id as retouch_id')
            ->join('p.orders', 'o')
            ->join('o.client', 'c')
            ->innerJoin('o.pictures', 'pi')
            ->leftJoin('pi.pictureDetail', 'pd')
            ->leftJoin('pd.retouch', 're')
            ->where('p.id = :promo_id')
            ->setParameter('promo_id', $promoId)
            ->AndWhere('c.id = :user_id')
            ->setParameter('user_id', $userId)
            ->andWhere('o.paymentDate IS NOT NULL')
            ->groupBy('retouch_id')
            ->getQuery()
            ->getResult()
        ;
    }
	
	/**
	 * count the useNumber & userImageNumber for a given promo
	 *
	 * @param int $promoId
	 *
	 * @return array (useNumber, userImageNumber)
	 */
    public function findThePicturesNumberByPromoGroupedByRetouchAndOrder(int $promoId)
    {
        return $this->createQueryBuilder('p')
            ->select('count(pd) as image_numbers', 're.id as retouch_id', 'o.id as order_id')
            ->join('p.orders', 'o')
            ->join('o.client', 'c')
            ->innerJoin('o.pictures', 'pi')
            ->leftJoin('pi.pictureDetail', 'pd')
            ->leftJoin('pd.retouch', 're')
            ->where('p.id = :promo_id')
            ->setParameter('promo_id', $promoId)
            ->andWhere('o.paymentDate IS NOT NULL')
            ->groupBy('order_id')
            ->addGroupBy('retouch_id')
            ->getQuery()
            ->getResult()
        ;
    }
	
	/**
	 * count the useNumber & userImageNumber for a given promo
	 *
	 * @param int $promoId
	 * @param int $userId
	 *
	 * @return array (useNumber, userImageNumber)
	 */
	public function findThePicturesNumberByPromoAndUserGroupedByRetouchAndOrder(int $promoId, int $userId)
	{
		return $this->createQueryBuilder('p')
			->select('count(pd) as image_numbers', 're.id as retouch_id', 'o.id as order_id')
			->join('p.orders', 'o')
			->join('o.client', 'c')
			->innerJoin('o.pictures', 'pi')
			->leftJoin('pi.pictureDetail', 'pd')
			->leftJoin('pd.retouch', 're')
			->where('p.id = :promo_id')
			->setParameter('promo_id', $promoId)
			->andWhere('c.id = :user_id')
			->setParameter('user_id', $userId)
			->andWhere('o.paymentDate IS NOT NULL')
			->groupBy('order_id')
			->addGroupBy('retouch_id')
			->getQuery()
			->getResult()
			;
	}
	
	/**
	 * count the useNumber & userImageNumber for a given promo
	 *
	 * @param int $promoId
	 *
	 * @return array (useNumber, userImageNumber)
	 */
    public function findTheUseNumberAndImageNumberGroupedByDate(int $promoId)
    {
        return $this->createQueryBuilder('p')
            ->select('o.paymentDate as date, sum(o.totalReductionOnPictures) as imageNumber')
            ->join('p.orders', 'o')
            ->innerJoin('o.pictures', 'pi')
            ->leftJoin('pi.pictureDetail', 'pd')
            ->where('p.id = :promo_id')
            ->setParameter('promo_id', $promoId)
            ->andWhere('o.paymentDate IS NOT NULL')
            ->groupBy('o.paymentDate')
            ->orderBy('date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
	
	/**
	 * @param array $data
	 * @return bool
	 */
	public function disableOrEnablePromosByIds(array $data): bool
	{
		$success = true;
		try {
			// suspend auto-commit
			$this->_em->beginTransaction();
			foreach ($this->findBy(['id' => $data]) as $promo) {
				$promo->setExpired($promo->getExpired() ? false : true);
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
