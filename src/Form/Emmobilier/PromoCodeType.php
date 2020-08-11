<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Validator\Constraints\PromoNotNull;
use App\Validator\Constraints\PromoType;
use App\Validator\Constraints\PromoDateInterval;
use App\Validator\Constraints\PromoExpiration;
use App\Validator\Constraints\PromoLimit;

use App\Form\Shared\Type\PromoSelectorType;

class PromoCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];

        $builder
            ->add('promoCode', PromoSelectorType::class, [
              'label' => 'orders.recap.promo.field',
              'attr' => ['class' => 'js-code-promo-input', 'autocomplete' => 'off', 'placeholder' => 'orders.recap.promo.field'],
              'constraints' => array(
                new PromoNotNull(['groups' => ['order_promo_validation']]),
                new PromoDateInterval(['groups' => ['order_promo_validation']]),
                new PromoExpiration(['groups' => ['order_promo_validation']]),
                new PromoLimit(['groups' => ['order_promo_validation'], 'user' => $user]),
                new PromoType(['groups' => ['order_promo_validation'], 'user' => $user])
              )
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
	        'csrf_protection' => false,
            'validation_groups' => array('order_promo_validation'),
            'user' => null
        ));
    }
}
