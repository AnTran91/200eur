<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PictureStatusType extends AbstractType
{
   /**
    * @var array
    */
    protected $options;

    public function __construct(array $pictureStatusOptions){
        $this->options = $pictureStatusOptions;
    }

    /**
    * {@inheritdoc}
    */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'choices' => array_flip($this->options),
          'attr' => ['class' => 'js-picture-status']
        ]);
    }

    /**
    * {@inheritdoc}
    */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
