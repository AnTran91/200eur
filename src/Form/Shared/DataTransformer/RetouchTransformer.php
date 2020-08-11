<?php

namespace App\Form\Shared\DataTransformer;

use App\Entity\Retouch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RetouchTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
	
	/**
	 * {@inheritdoc}
	 */
    public function transform($retouchs)
    {
        if (!is_array($retouchs)) {
            return '';
        }

        return $retouchs;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($ids)
    {
        // no id number? It's optional, so that's ok
        if (empty($ids)) {
            return null;
        }

        $retouchs = $this->entityManager
            ->getRepository(Retouch::class)
            // query for the Retouch with this id
            ->findByArrayOfIds($ids)
        ;

        if (empty($retouchs)) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An Retouch with number "%s" does not exist!',
                explode(',', $ids)
            ));
        }
        return $retouchs;
    }
}
