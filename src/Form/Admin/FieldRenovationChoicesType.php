<?php

namespace App\Form\Admin;

use App\Entity\FieldRenovationChoices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FieldRenovationChoicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('picturePath', TextType::class, [
            'label' => 'admin.field_renovation_choice.picture_path',
            'attr' => ['class' => 'js-picture-path-field']
          ])
          ->add('uuid', HiddenType::class, [
            'label' => false,
            'attr' => ['class' => 'js-picture-uuid']
          ])
      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FieldRenovationChoices::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'field_renovation_choice';
    }
}
