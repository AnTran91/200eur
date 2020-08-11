<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\OrderDeliveryTime;
use Symfony\Component\Translation\TranslatorInterface;

class OrderDeliveryTimeType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('deliveryTime', EntityType::class, array(
                    'label' => false,
                    'class' => OrderDeliveryTime::class,
                    'choice_label' => function (OrderDeliveryTime $deliveryTime) {
                        return $this->translator->trans('orders.field.delivery', ['%time' => sprintf('%s%s', $deliveryTime->getTime(), $this->translator->trans($deliveryTime->getUnit()))]) ;
                    },
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.global = True')
                            ->andWhere('u.appType = :appType')
                            ->setParameter('appType', OrderDeliveryTime::EMMOBILIER_TYPE)
                            ->orderBy('u.time', 'ASC')
                            ;
                    },
                    'attr' => ['class' => 'js-order-delivery-time'],
                    'expanded' => true,
                    'multiple' => false,
                    'block_name' => 'time'
              ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'order_delivery';
    }
}
