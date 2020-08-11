<?php

namespace App\Form\Admin;

use App\Entity\Wallet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class WalletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentAmount', TextType::class, [
            	'data' => '0.00',
            	'label' => 'admin.wallet.wallet'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Wallet::class,
            'translation_domain' => 'admin',
            'validation_groups' => ['AdminUserCreation']
        ]);
    }
}
