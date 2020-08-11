<?php

namespace App\Form\Shared\Type;

use App\Entity\BillingAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BillingAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', TextType::class, array('label' => 'admin.user.billing_address.company'))
            ->add('address', TextareaType::class, array('label' => 'admin.user.billing_address.address'))
            ->add('secondaryAddress', TextareaType::class, array('label' => 'admin.user.billing_address.secondary_address'))
            ->add('zipCode', TextType::class, array('label' => 'admin.user.billing_address.zip'))
            ->add('city', TextType::class, array('label' => 'admin.user.billing_address.city'))
            ->add('country', CountryType::class, array('label' => 'admin.user.billing_address.country', 'preferred_choices' => array('FR'),))
            ->add('phone', IntegerType::class, array('label' => 'admin.user.billing_address.phone'))
            ->add('TVA', TextType::class, array('label' => 'admin.user.billing_address.vat'))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => BillingAddress::class,
            'validation_groups' => array('EmmobilierProfile'),

        ]);
    }
}
