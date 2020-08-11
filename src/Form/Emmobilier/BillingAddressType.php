<?php

namespace App\Form\Emmobilier;

use App\Entity\BillingAddress;
use Symfony\Component\Form\AbstractType;
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
            ->add('firstName', TextType::class, array('label' => 'user.field.first_name', 'label_attr' => ['required' => true]))
            ->add('lastName', TextType::class, array('label' => 'user.field.last_name', 'label_attr' => ['required' => true]))
            ->add('company', TextType::class, array('label' => 'user.field.company'))
            ->add('networkName', TextType::class, array('label' => 'user.field.network_name'))
            ->add('address', TextareaType::class, array('label' => 'user.field.address', 'label_attr' => ['required' => true], 'attr' => ['rows' => '5', 'cols' => '50']))
            ->add('secondaryAddress', TextareaType::class, array('label' => 'user.field.secondary_address', 'attr' => ['rows' => '5', 'cols' => '50']))
            ->add('zipCode', TextType::class, array('label' => 'user.field.zip_code'))
            ->add('city', TextType::class, array('label' => 'user.field.city'))
            ->add('country', CountryType::class, array('label' => 'user.field.country', 'preferred_choices' => array('FR'), 'attr' => ['class' => 'js-billing-address-country']))
            ->add('phone', TextType::class, array('label' => 'user.field.phone'))
            ->add('corporateName', TextType::class, array('label' => 'user.field.corporate_name'))
            ->add('TVA', TextType::class, array('label' => 'user.field.TVA'))
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
