<?php
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\User;


class MonthlyOrderType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
          ->add('from', DateType::class, ['label' => 'admin.email.to'])
          ->add('to', DateType::class, ['label' => 'admin.email.subject'])
          ->add('user', EntityType::class, ['class' => User::class,
                                            'placeholder' => '', 'label' => 'admin.user.user'])
          ->add('Generate', SubmitType::class, array('label' => 'Generate'));
  }

  public function configureOptions(OptionsResolver $resolver){
      $resolver->setDefaults([
        'translation_domain' => 'admin',
      ]);
  }
}