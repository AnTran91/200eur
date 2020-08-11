<?php

namespace App\Form\Admin;

use App\Entity\Agency;
use App\Entity\User;
use App\Entity\Network;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AgencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'admin.agency.name'])
            ->add('owner', EntityType::class, [
              'class' => User::class,
              'label' => 'admin.common.owner',
              'attr' => ['class' => 'js-select-selector']
            ])
            ->add('phone', TextType::class, ['label' => 'admin.agency.phone'])
            ->add('address', TextType::class, ['label' => 'admin.agency.address'])
            ->add('network', EntityType::class, [
              'class' => Network::class,
              'label' => 'admin.network.network',
              'placeholder' => '',
              'attr' => ['class' => 'js-select-selector']
            ])
            ->add('employees', EntityType::class, [
              'class' => User::class,
              'label' => 'admin.user.user',
              'multiple' => True,
              'placeholder' => '',
              'by_reference' => false,
              'attr' => ['class' => 'js-multiselect-selector']
            ])
            ->add('registrationCode', TextType::class, ['label' => 'admin.common.registration_code'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Agency::class,
            'translation_domain' => 'admin',
        ]);
    }
}
