<?php

namespace App\Repository;

use App\Entity\FieldGroup;
use App\Entity\Retouch;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FieldGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldGroup[]    findAll()
 * @method FieldGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FieldGroup::class);
    }

    /**
     * @return FieldGroup[] Returns an array of FieldGroup objects
     *
     * fallback to default values in case if record is not translated
     */
    public function findFormFieldsByRetouchs(string $locale, array $retouchs)
    {
        $qb = $this->createQueryBuilder('fg')
                    ->addSelect('fg', 'f', 'c', 'fp', 'r', 'd', 'dl')
                    ->leftJoin('fg.retouch', 'r')
                    ->join('fg.fields', 'f')
                    ->leftJoin('f.choices', 'c')
                    ->leftJoin('f.disabledOn', 'd')
                    ->leftJoin('f.disabledFields', 'dl')
                    ->leftJoin('f.renovations', 'fp');

        return $qb->where($qb->expr()->isNull('r'))
                    ->OrWhere($qb->expr()->in('r', ':ids'))
                    ->setParameter('ids', $retouchs)
                    ->orderBy('fg.orderNumber', 'ASC')
                    ->addOrderBy('f.orderNumber', 'ASC')
                    ->getQuery()
                    ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
                    ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
                    ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
                    ->getResult()
        ;
    }

    /**
     * @return FieldGroup[] Returns an array of FieldGroup objects
     *
     * fallback to default values in case if record is not translated
     */
    public function findFormFieldsByRetouch(string $locale, Retouch $retouch): ?array
    {
        $qb = $this->createQueryBuilder('fg')
                     ->addSelect('fg', 'f', 'c', 'fp', 'r', 'd', 'dl')
                     ->leftJoin('fg.retouch', 'r')
                     ->join('fg.fields', 'f')
                     ->leftJoin('f.disabledOn', 'd')
                     ->leftJoin('f.disabledFields', 'dl')

                     ->leftJoin('f.choices', 'c')
                     ->leftJoin('f.renovations', 'fp');

        return $qb->where($qb->expr()->isNull('r'))
           ->OrWhere($qb->expr()->eq('r', ':id'))
           ->setParameter('id', $retouch->getId())
           ->orderBy('fg.orderNumber', 'ASC')
           ->addOrderBy('f.orderNumber', 'ASC')
           ->getQuery()
           ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
           ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
           ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
           ->getResult()
         ;
    }
}
