<?php

namespace App\Repository;

use App\Entity\PictureDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PictureDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method PictureDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method PictureDetails[]    findAll()
 * @method PictureDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureDetailsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PictureDetails::class);
    }
}
