<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LanguageType extends AbstractType
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Form configuration
     *
     * @param array $languageOptions
     */
    public function __construct(array $languageOptions)
    {
        $this->options = $languageOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'choices' => $this->options
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
