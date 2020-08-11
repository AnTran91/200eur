<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrderFilterStatusType extends AbstractType
{
    /**
     * @var array
     */
    protected $options;
	
	/**
	 * Form configuration
	 *
	 * @param array $orderStatusFilterOptions
	 */
    public function __construct(array $orderStatusFilterOptions)
    {
        $this->options = $orderStatusFilterOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'choices' => array_flip($this->options)
      ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
