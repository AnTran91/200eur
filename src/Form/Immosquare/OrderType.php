<?php

namespace App\Form\Immosquare;

use App\Entity\OrderDeliveryTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delivery_time', EntityType::class, [
                'label' => false,
                'class' => OrderDeliveryTime::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.global = True')
                        ->andWhere('u.appType = :appType')
                        ->setParameter('appType', OrderDeliveryTime::IMMOSQUARE_TYPE)
                        ;
                },
                'choice_value' => function (OrderDeliveryTime $entity = null) {
                    return $entity ? $entity->getOrderDeliveryCode() : '';
                },
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('images', CollectionType::class, [
                'label' => false,
                'allow_add' => true,
                'by_reference' => false,
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => false],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }

}