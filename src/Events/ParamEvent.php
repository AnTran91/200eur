<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\OptionsResolver\OptionsResolver;
use \ArrayAccess;

class ParamEvent extends Event implements ArrayAccess
{
    /**
     * @var array
     */
    private $container = array();
    private $violations = array();

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        $this->container = $resolver->resolve($options);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * configure OptionsResolver
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'request' => null,
            'userDir' => null,
            'pictureDir' => null,
            'picture' => null,
            'formData' => array(),
            'pictureDetail' => null,
            'retouch' => null,
        ));

        $resolver->setAllowedTypes('request', ['null', '\Symfony\Component\HttpFoundation\Request']);
        $resolver->setAllowedTypes('picture', ['null', '\App\Entity\Picture']);
        $resolver->setAllowedTypes('pictureDetail', ['null', '\App\Entity\PictureDetails']);
        $resolver->setAllowedTypes('userDir', ['null', 'string']);
        $resolver->setAllowedTypes('formData', ['null', 'array']);
        $resolver->setAllowedTypes('pictureDir', ['null', 'string']);

        $resolver->setRequired(['request', 'userDir']);
    }

    /**
     * Set the value of violations
     *
     * @param mixed violations
     *
     * @return self
     */
    public function setViolations(?array $violations): self
    {
        $this->violations = $violations;

        return $this;
    }

    /**
     * Get the value of violations
     *
     * @return mixed
     */
    public function getViolations(): ?array
    {
        return $this->violations;
    }
}
