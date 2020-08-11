<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class FileDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uuid', TextType::class)
            ->add('file_name', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data' => null,
            'csrf_protection' => false,
        ]);
    }

    /**
     * This will remove formTypeName from the form
     *
     * @return null
     */
    public function getBlockPrefix()
    {
        return null;
    }
}
