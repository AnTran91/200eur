<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Form\Shared\Type\OrderFilterStatusType;

class OrderFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', OrderFilterStatusType::class, [
                'label' => 'orders.field.status',
                'attr' => ['class' => 'custom-select mr-sm-2 js-order-status'],
                'placeholder' => 'order.status.all'
              ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null
        ]);
    }
}
