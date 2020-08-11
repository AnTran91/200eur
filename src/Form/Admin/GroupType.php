<?php

namespace App\Form\Admin;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class GroupType extends AbstractType
{
    /**
     * @var array
     */
    private $roles;

    public function __construct(array $roleHierarchy)
    {
        $this->roles = $roleHierarchy;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
              'label' => 'admin.group.name',
              'constraints' => [new Assert\NotBlank()]
            ])
            ->add('roles', ChoiceType::class, [
              'label' => 'admin.group.roles',
              'choices' => $this->roles,
              'multiple' => true,
              'attr' => ['class' => 'js-roles-selector'],
              'constraints' => [new Assert\NotBlank()]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
            'translation_domain' => 'admin'
        ]);
    }
}
