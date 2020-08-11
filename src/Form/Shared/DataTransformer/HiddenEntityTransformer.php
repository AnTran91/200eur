<?php

namespace App\Form\Shared\DataTransformer;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityHiddenTransformer
 */
class HiddenEntityTransformer implements DataTransformerInterface
{
    /**
    * @var string
    */
    protected $class;

    /**
    * @var string
    */
    protected $property;

    /**
    * @var EntityManager
    */
    protected $em;

    /**
     * @param ObjectManager $objectManager
     * @param string          $class
     * @param string          $property
     */
    public function __construct(ObjectManager $objectManager, $class, $property = "id")
    {
        $this->class = $class;
        $this->property = $property;
        $this->em = $objectManager;
    }

    /**
     * @param mixed $entity
     *
     * @return mixed|null
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        if (!$entity instanceof $this->class) {
            throw new TransformationFailedException(sprintf('Object must be instance of %s, instance of %s has given.', $this->class, get_class($entity)));
        }
        $methodName = 'get' . ucfirst($this->property);
        if (!method_exists($entity, $methodName)) {
            throw new InvalidConfigurationException(sprintf('There is no getter for property "%s" in class "%s".', $this->property, $this->class));
        }
        return $entity->{$methodName}();
    }
	
	/**
	 * @param $identifier
	 * @return mixed|null|object
	 */
    public function reverseTransform($identifier)
    {
        if (!$identifier) {
            return null;
        }
        $entity = $this->em->getRepository($this->class)
                            ->find($identifier);

        if (null === $entity) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $this->class, $this->property));
        }
        return $entity;
    }
}
