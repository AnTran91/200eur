<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class YearType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array_combine(range(date("Y"), date("Y")+20), range(date("Y"), date("Y")+20)),
            'preferred_choices' => function ($val, $key) {
                // prefer options within 3 days
                return $key == date("Y");
            },
            'choice_translation_domain' => false
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
