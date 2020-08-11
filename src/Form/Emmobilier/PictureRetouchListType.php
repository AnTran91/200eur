<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Form\Shared\Type\RetouchEntityType;

class PictureRetouchListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('retouchs', RetouchEntityType::class, [
                'label' => false,
                'row' => $options['row'],
                'current_locale' => $options['locale']
            ])
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'row' => '4',
            'locale' => 'FR_fr'
        ]);
    }
}
