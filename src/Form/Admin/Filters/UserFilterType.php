<?php

namespace App\Form\Admin\Filters;

use App\Entity\Agency;
use App\Entity\Network;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserFilterType extends AbstractType
{
    private $roles;


    public function __construct(array $roleHierarchy)
    {
      $this->roles = $roleHierarchy;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'admin.user.first_name'])
            ->add('lastName', TextType::class, ['label' => 'admin.user.last_name'])
            ->add('email', EmailType::class, ['label' => 'admin.user.email'])
            ->add('roles', ChoiceType::class, [
              'label' => 'admin.user.roles',
              'choices' => $this->roles,
              'placeholder' => 'admin.common.none',
              'attr' => ['class' => 'js-select-list'],
            ])
            ->add('agency', EntityType::class, [
              'class' => Agency::class,
	          'attr' => ['class' => 'js-select-list'],
              'label' => 'admin.agency.agency',
              'placeholder' => 'admin.common.none'
            ])
            ->add('network', EntityType::class, [
              'class' => Network::class,
	          'attr' => ['class' => 'js-select-list'],
              'label' => 'admin.network.network',
              'placeholder' => 'admin.common.none'
            ])
            ->add('createdAt', DateIntervalType::class, [
              'label' => 'admin.common.date',
              'widget' => 'single_text',
              // adds a class that can be selected in JavaScript
              'attr' => ['class' => 'js-datepicker form-control', 'autocomplete' => 'off'],
            ])
            ->add('enabled', ChoiceType::class, [
              'label' => 'admin.user.enabled',
              'choices' => ['Activé' => 1, 'Non Activé' => 0],
              'placeholder' => 'admin.common.none',
              'attr' => ['class' => 'js-select-list'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin',
            'csrf_protection' => false
        ]);
    }
	
	/**
	 * This will remove formTypeName from the form
	 *
	 * @return null|string
	 */
    public function getBlockPrefix(): ?string
    {
        return 'filter';
    }
}
