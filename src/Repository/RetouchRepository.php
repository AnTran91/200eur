<?php

namespace App\Repository;

use App\Entity\Retouch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Retouch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retouch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retouch[]    findAll()
 * @method Retouch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetouchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Retouch::class);
    }

    /**
     * Get all retouch id objects.
     *
     * @return Retouch[] Returns an array of Retouch objects
     */
    public function findByEmmobilierType()
    {
        return $this->createQueryBuilder('r')
            ->select('r.id as id')
            ->where('r.appType = :retouch_type')
            ->setParameter('retouch_type', Retouch::EMMOBILIER_TYPE)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Get all retouch objects & fallback to default values in case if the retouchs is not translated.
     *
     * @param string $locale
     * @return Retouch[] Returns an array of Retouch objects
     */
    public function findByEmmobilierTypeWithFallback(string $locale = 'En_en')
    {
        return $this->createQueryBuilder('r')
            ->addSelect('pricings', 'order_delivery_time')
            ->join('r.pricings', 'pricings')
            ->join('pricings.orderDeliveryTime', 'order_delivery_time')
            ->where('r.appType = :retouch_type')
            ->setParameter('retouch_type', Retouch::EMMOBILIER_TYPE)
            ->orderBy('r.orderNumber', 'ASC')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
            ->getResult()
        ;
    }

    /**
     * Get one retouch by id & fallback to default values in case if the retouchs is not translated.
     *
     * @param int $id
     * @param null|string $locale
     * @return Retouch Returns an array of Retouch objects
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(?string $locale, int $id): ?Retouch
    {
        return $this->createQueryBuilder('r')
            ->addSelect('pricings', 'order_delivery_time')
            ->join('r.pricings', 'pricings')
            ->join('pricings.orderDeliveryTime', 'order_delivery_time')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->orderBy('r.orderNumber', 'ASC')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get all retouch objects & fallback to default values in case if the retouchs is not translated.
     *
     * @param array $ids
     * @param null|string $locale
     *
     * @return Retouch[] Returns an array of Retouch objects
     */
    public function findByArrayOfIdsAndLocale(array $ids, ?string $locale = null)
    {
        return $this->createQueryBuilder('r')
            ->addSelect('pricings', 'orderDeliveryTime')
            ->join('r.pricings', 'pricings')
            ->join('pricings.orderDeliveryTime', 'orderDeliveryTime')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
            ->getResult()
        ;
    }

    /**
     * Get all retouch objects & fallback to default values in case if the retouchs is not translated.
     *
     * @param array $ids
     * @return Retouch[] Returns an array of Retouch objects
     */
    public function findByArrayOfIds(array $ids)
    {
        return $this->createQueryBuilder('r')
            ->addSelect('pricings', 'orderDeliveryTime')
            ->join('r.pricings', 'pricings')
            ->join('pricings.orderDeliveryTime', 'orderDeliveryTime')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
            ->getResult()
        ;
    }

    /**
     * @return array
     */
    public function findAPIRetouchChoices(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.retouchCode as api_code')
            ->where('r.appType = :retouch_type')
            ->setParameter('retouch_type', Retouch::IMMOSQUARE_TYPE)
            ->getQuery()
            ->getResult()
        ;
    }
}
