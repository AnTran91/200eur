<?php

namespace App\Form\Admin;

use App\Entity\OrderDeliveryTime;
use App\Entity\PhotoRetouchingPricing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Translation\TranslatorInterface;

class RetouchPriceType extends AbstractType
{
    /**
     * @var array
     */
    private $appTypeOptions;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor
     *
     * @param TranslatorInterface   $translator
     * @param array                 $applicationsTypes
     */
    public function __construct(TranslatorInterface $translator, array $applicationsTypes)
    {
        $this->translator = $translator;
        $this->appTypeOptions = $applicationsTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', MoneyType::class, [
              'label' => 'admin.retouch.price'
            ])
            ->add('orderDeliveryTime', EntityType::class, array(
                'label' => 'admin.retouch.order_delivery_time',
                'placeholder' => 'admin.common.none',
                'class' => OrderDeliveryTime::class,
                'choice_attr' => function (OrderDeliveryTime $entity, $key, $value) {
                    // adds a class like attending_yes, attending_no, etc
                    if (!is_null($entity->getAppType())) {
                        return ['class' => $entity->getAppType()];
                    }
                    return [];
                },
                'choice_label' => function (OrderDeliveryTime $deliveryTime) {
                    return $this->translator->trans('admin.retouch.delivery', [
                        '%time' => $deliveryTime->getTime() ,
                        '%unit' => $this->translator->trans($deliveryTime->getUnit(), [], 'admin')
                    ], 'admin') ;
                },
                'attr' => ['class' => 'js-delivery-time']
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhotoRetouchingPricing::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'retouch_price';
    }
}
