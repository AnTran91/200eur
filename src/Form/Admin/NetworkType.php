<?php

namespace App\Form\Admin;

use App\Entity\Network;
use App\Entity\User;
use App\Entity\Agency;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'admin.network.name'])
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'label' => 'admin.common.owner',
                'attr' => ['class' => 'js-select-selector']
              ])
            ->add('agencies', EntityType::class, [
              'class' => Agency::class,
              'label' => 'admin.agency.agency',
              'multiple' => True,
              'placeholder' => '',
              'by_reference' => false,
              'attr' => ['class' => 'js-multiselect-selector']
            ])
            ->add('employees', EntityType::class, [
              'class' => User::class,
              'label' => 'admin.user.user',
              'multiple' => True,
              'by_reference' => false,
              'placeholder' => '',
              'attr' => ['class' => 'js-multiselect-selector']
            ])
            ->add('registrationCode', TextType::class, ['label' => 'admin.common.registration_code'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Network::class,
            'translation_domain' => 'admin',
        ]);
    }
}
