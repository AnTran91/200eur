<?php

namespace App\Repository;

use App\Entity\Field;
use App\Entity\Retouch;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Field|null find($id, $lockMode = null, $lockVersion = null)
 * @method Field|null findOneBy(array $criteria, array $orderBy = null)
 * @method Field[]    findAll()
 * @method Field[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Field::class);
    }

    /**
     * @return Field[] Returns an array of Field objects
     */
    public function findDefaultValuesFromRetouchs(array $retouchs)
    {
        return $this->createQueryBuilder('f')
            ->addSelect('fg', 'c', 'fp', 'r', 'd', 'dl')
            ->join('f.fieldGroup', 'fg')
            ->leftJoin('fg.retouch', 'r')
            ->leftJoin('f.choices', 'c')
            ->leftJoin('f.disabledOn', 'd')
            ->leftJoin('f.disabledFields', 'dl')
            ->leftJoin('f.renovations', 'fp')
            ->where('r IS NULL')
            ->OrWhere('r.id in (:ids)')
            ->setParameter('ids', $retouchs)
            ->orderBy('f.orderNumber')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->getResult()
        ;
    }

    /**
     * Get all filed objects & fallback to default values in case if the retouchs is not translated.
     *
     * @return Retouch[] Returns an array of Retouch objects
     */
     public function findAllByFieldThatHavePrice(?string $locale = null)
     {
         return $this->createQueryBuilder('f')
             ->addSelect('fg', 'c', 'fp', 'r', 'd', 'dl')
             ->join('f.fieldGroup', 'fg')
             ->leftJoin('fg.retouch', 'r')
             ->leftJoin('f.choices', 'c')
             ->leftJoin('f.disabledOn', 'd')
             ->leftJoin('f.disabledFields', 'dl')
             ->leftJoin('f.renovations', 'fp')
             ->where('f.price IS NOT NULL')
             ->getQuery()
             ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
             ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
             ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
             ->getResult()
         ;
     }

    /**
     * @return Field[] Returns an array of Field objects
     */
    public function findDefaultValuesFromRetouch(int $retouchId)
    {
        return $this->createQueryBuilder('f')
            ->addSelect('fg', 'fp', 'r', 'd', 'dl')
            ->join('f.fieldGroup', 'fg')
            ->leftJoin('fg.retouch', 'r')
            ->leftJoin('f.disabledOn', 'd')
            ->leftJoin('f.disabledFields', 'dl')
            ->leftJoin('f.renovations', 'fp')
            ->where('r IS NULL')
            ->OrWhere('r.id = :id')
            ->setParameter('id', $retouchId)
            ->orderBy('f.orderNumber')
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->getResult()
        ;
    }
}
