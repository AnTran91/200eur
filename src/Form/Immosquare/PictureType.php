<?php

namespace App\Form\Immosquare;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints\URLAndFile;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new URLAndFile()
                ]
            ])
            ->add('services', CollectionType::class, [
                'label' => false,
                'entry_type' => PictureDetailType::class,
                'entry_options' => array(
                    'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
            'allow_extra_fields' => true
        ]);
    }


}